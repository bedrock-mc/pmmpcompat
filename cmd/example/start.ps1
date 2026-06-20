param(
    [Parameter(ValueFromRemainingArguments = $true)]
    [string[]] $ServerArgs
)

$ErrorActionPreference = "Stop"

$Dir = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $Dir

$PhpTag = if ($env:PMMPCOMPAT_PHP_TAG) { $env:PMMPCOMPAT_PHP_TAG } else { "pm5-php-8.2-latest" }
$PhpReleaseUrl = "https://github.com/pmmp/PHP-Binaries/releases/download/$PhpTag"
$PhpDir = if ($env:PMMPCOMPAT_PHP_DIR) { $env:PMMPCOMPAT_PHP_DIR } else { $Dir }
$PluginsDir = if ($env:PMMPCOMPAT_PLUGINS) { $env:PMMPCOMPAT_PLUGINS } else { Join-Path $Dir "plugins" }
$DataDir = if ($env:PMMPCOMPAT_DATA) { $env:PMMPCOMPAT_DATA } else { Join-Path $Dir "data" }

if (-not [Environment]::Is64BitOperatingSystem) {
    throw "PMMP publishes Windows PHP binaries only for x64 in $PhpTag."
}

$Archive = "PHP-8.2-Windows-x64-PM5.zip"
$PhpCandidates = @(
    (Join-Path $PhpDir "bin/php7/php.exe"),
    (Join-Path $PhpDir "bin/php7/bin/php.exe"),
    (Join-Path $PhpDir "bin/php/php.exe")
)
$PhpBin = $PhpCandidates | Where-Object { Test-Path $_ } | Select-Object -First 1

if (-not $env:PMMPCOMPAT_PHP -and -not $PhpBin) {
    New-Item -ItemType Directory -Force -Path $PhpDir | Out-Null
    $Temp = New-Item -ItemType Directory -Force -Path (Join-Path ([IO.Path]::GetTempPath()) ("pmmpcompat-php-" + [Guid]::NewGuid()))
    try {
        $ArchivePath = Join-Path $Temp.FullName $Archive
        Write-Host "Downloading PMMP PHP binary: $Archive"
        Invoke-WebRequest -Uri "$PhpReleaseUrl/$Archive" -OutFile $ArchivePath
        Expand-Archive -Path $ArchivePath -DestinationPath $PhpDir -Force
    } finally {
        Remove-Item -Recurse -Force $Temp.FullName -ErrorAction SilentlyContinue
    }
    $PhpBin = $PhpCandidates | Where-Object { Test-Path $_ } | Select-Object -First 1
}

if (-not $env:PMMPCOMPAT_PHP) {
    if ($PhpBin) {
        $env:PMMPCOMPAT_PHP = $PhpBin
    } else {
        $SystemPhp = Get-Command php -ErrorAction SilentlyContinue
        if ($SystemPhp) {
            $env:PMMPCOMPAT_PHP = $SystemPhp.Source
        } else {
            throw "No PHP binary found. Set PMMPCOMPAT_PHP manually."
        }
    }
}

if (-not $env:PMMPCOMPAT_PHP_ARGS) {
    $PhpArgs = @("-d", "error_reporting=8191")
    $ExtensionCandidates = @(
        (Join-Path $PhpDir "bin/php7/ext"),
        (Join-Path $PhpDir "bin/php/ext")
    )
    $ExtensionDir = $ExtensionCandidates | Where-Object { Test-Path $_ } | Select-Object -First 1
    if ($ExtensionDir) {
        $PhpArgs += @("-d", "extension_dir=$ExtensionDir")
    }
    $env:PMMPCOMPAT_PHP_ARGS = $PhpArgs -join " "
}

New-Item -ItemType Directory -Force -Path $PluginsDir | Out-Null
New-Item -ItemType Directory -Force -Path $DataDir | Out-Null
$env:PMMPCOMPAT_PLUGINS = $PluginsDir
$env:PMMPCOMPAT_DATA = $DataDir

Write-Host "PHP: $env:PMMPCOMPAT_PHP"
Write-Host "Plugins: $env:PMMPCOMPAT_PLUGINS"
Write-Host "Data: $env:PMMPCOMPAT_DATA"

& go run . @ServerArgs
exit $LASTEXITCODE
