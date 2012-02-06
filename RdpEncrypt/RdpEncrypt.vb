Imports System
Imports System.Text
Imports System.Runtime.InteropServices
Imports System.ComponentModel
Imports Microsoft.VisualBasic

Public Module RdpEncrypt

    Public Class DPAPI
        <DllImport("crypt32.dll", SetLastError:=True, CharSet:=CharSet.Auto)> _
        Private Shared Function CryptProtectData( _
            ByRef pPlainText As DATA_BLOB, _
            ByVal szDescription As String, _
            ByRef pEntropy As DATA_BLOB, _
            ByVal pReserved As IntPtr, _
            ByRef pPrompt As CRYPTPROTECT_PROMPTSTRUCT, _
            ByVal dwFlags As Integer, _
            ByRef pCipherText As DATA_BLOB _
        ) As Boolean
        End Function

        <DllImport("crypt32.dll", SetLastError:=True, CharSet:=CharSet.Auto)> _
        Private Shared Function CryptUnprotectData( _
            ByRef pCipherText As DATA_BLOB, _
            ByRef pszDescription As String, _
            ByRef pEntropy As DATA_BLOB, _
            ByVal pReserved As IntPtr, _
            ByRef pPrompt As CRYPTPROTECT_PROMPTSTRUCT, _
            ByVal dwFlags As Integer, _
            ByRef pPlainText As DATA_BLOB _
        ) As Boolean
        End Function

        <StructLayout(LayoutKind.Sequential, CharSet:=CharSet.Unicode)> _
        Friend Structure DATA_BLOB
            Public cbData As Integer
            Public pbData As IntPtr
        End Structure

        <StructLayout(LayoutKind.Sequential, CharSet:=CharSet.Unicode)> _
        Friend Structure CRYPTPROTECT_PROMPTSTRUCT
            Public cbSize As Integer
            Public dwPromptFlags As Integer
            Public hwndApp As IntPtr
            Public szPrompt As String
        End Structure

        Private Const CRYPTPROTECT_UI_FORBIDDEN As Integer = 1
        Private Const CRYPTPROTECT_LOCAL_MACHINE As Integer = 4

        Private Shared Sub InitPrompt _
        ( _
            ByRef ps As CRYPTPROTECT_PROMPTSTRUCT _
        )
            ps.cbSize = Marshal.SizeOf(GetType(CRYPTPROTECT_PROMPTSTRUCT))
            ps.dwPromptFlags = 0
            ps.hwndApp = IntPtr.Zero
            ps.szPrompt = Nothing
        End Sub

        Private Shared Sub InitBLOB _
        ( _
            ByVal data As Byte(), _
            ByRef blob As DATA_BLOB _
        )
            ' Use empty array for null parameter.
            If data Is Nothing Then
                data = New Byte(0) {}
            End If

            ' Allocate memory for the BLOB data.
            blob.pbData = Marshal.AllocHGlobal(data.Length)

            ' Make sure that memory allocation was successful.
            If blob.pbData.Equals(IntPtr.Zero) Then
                Throw New Exception( _
                        "Unable to allocate data buffer for BLOB structure.")
            End If

            ' Specify number of bytes in the BLOB.
            blob.cbData = data.Length
            Marshal.Copy(data, 0, blob.pbData, data.Length)
        End Sub

        Public Enum KeyType
            UserKey = 1
            MachineKey
        End Enum

        Private Shared defaultKeyType As KeyType = KeyType.UserKey

        Public Shared Function Encrypt _
        ( _
            ByVal keyType As KeyType, _
            ByVal plainText As String, _
            ByVal entropy As String, _
            ByVal description As String _
        ) As String
            If plainText Is Nothing Then
                plainText = String.Empty
            End If
            If entropy Is Nothing Then
                entropy = String.Empty
            End If

            Dim result As Byte()
            Dim encrypted As String = ""
            Dim i As Integer
            result = Encrypt(keyType, _
                             Encoding.Unicode.GetBytes(plainText), _
                             Encoding.Unicode.GetBytes(entropy), _
                             description)
            For i = 0 To result.Length - 1
                encrypted = encrypted & Convert.ToString(result(i), 16).PadLeft(2, "0").ToUpper()
            Next
            Return encrypted.ToString()
        End Function

        Public Shared Function Encrypt _
        ( _
            ByVal keyType As KeyType, _
            ByVal plainTextBytes As Byte(), _
            ByVal entropyBytes As Byte(), _
            ByVal description As String _
        ) As Byte()
            If plainTextBytes Is Nothing Then
                plainTextBytes = New Byte(0) {}
            End If

            If entropyBytes Is Nothing Then
                entropyBytes = New Byte(0) {}
            End If

            If description Is Nothing Then
                description = String.Empty
            End If

            Dim plainTextBlob As DATA_BLOB = New DATA_BLOB
            Dim cipherTextBlob As DATA_BLOB = New DATA_BLOB
            Dim entropyBlob As DATA_BLOB = New DATA_BLOB

            Dim prompt As  _
                    CRYPTPROTECT_PROMPTSTRUCT = New CRYPTPROTECT_PROMPTSTRUCT
            InitPrompt(prompt)

            Try
                Try
                    InitBLOB(plainTextBytes, plainTextBlob)
                Catch ex As Exception
                    Throw New Exception("Cannot initialize plaintext BLOB.", ex)
                End Try

                Try
                    InitBLOB(entropyBytes, entropyBlob)
                Catch ex As Exception
                    Throw New Exception("Cannot initialize entropy BLOB.", ex)
                End Try

                Dim flags As Integer = CRYPTPROTECT_UI_FORBIDDEN

                If keyType = keyType.MachineKey Then
                    flags = flags Or (CRYPTPROTECT_LOCAL_MACHINE)
                End If

                Dim success As Boolean = CryptProtectData( _
                                                plainTextBlob, _
                                                description, _
                                                entropyBlob, _
                                                IntPtr.Zero, _
                                                prompt, _
                                                flags, _
                                                cipherTextBlob)

                If Not success Then
                    Dim errCode As Integer = Marshal.GetLastWin32Error()

                    Throw New Exception("CryptProtectData failed.", _
                                    New Win32Exception(errCode))
                End If

                Dim cipherTextBytes(cipherTextBlob.cbData) As Byte

                Marshal.Copy(cipherTextBlob.pbData, cipherTextBytes, 0, _
                                cipherTextBlob.cbData)

                Return cipherTextBytes
            Catch ex As Exception
                Throw New Exception("DPAPI was unable to encrypt data.", ex)
            Finally
                If Not (plainTextBlob.pbData.Equals(IntPtr.Zero)) Then
                    Marshal.FreeHGlobal(plainTextBlob.pbData)
                End If

                If Not (cipherTextBlob.pbData.Equals(IntPtr.Zero)) Then
                    Marshal.FreeHGlobal(cipherTextBlob.pbData)
                End If

                If Not (entropyBlob.pbData.Equals(IntPtr.Zero)) Then
                    Marshal.FreeHGlobal(entropyBlob.pbData)
                End If
            End Try
        End Function

    End Class

    Sub Main(ByVal args As String())
        Try
            Dim text As String = args(0)
            Dim encrypted As String

            encrypted = DPAPI.Encrypt(DPAPI.KeyType.MachineKey, text, Nothing, "psw")

            Console.WriteLine("{0}" & Chr(13) & Chr(10), encrypted)

        Catch ex As Exception
            While Not (ex Is Nothing)
                Console.WriteLine(ex.Message)
                ex = ex.InnerException
            End While
        End Try
    End Sub

End Module

