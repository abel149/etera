Get-ChildItem -Recurse -Filter *.php | ForEach-Object {
    & "C:\xampp\php\php.exe" -l $_.FullName
}
