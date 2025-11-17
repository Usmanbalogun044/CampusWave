# run-gateway.ps1
# Helper to start wa-gateway using WHATSAPP_API_KEY from the parent Laravel .env

Push-Location -Path $PSScriptRoot
try {
    $envFile = Join-Path $PSScriptRoot '..\.env'
    if (-Not (Test-Path $envFile)) { Write-Error "Cannot find parent .env at $envFile"; exit 1 }
    $content = Get-Content $envFile | Where-Object { $_ -match '^WHATSAPP_API_KEY=' }
    if ($content) {
        $secret = ($content -replace '^WHATSAPP_API_KEY=', '').Trim()
    } else {
        Write-Host "WHATSAPP_API_KEY not found in parent .env — using interactive prompt"
        $secret = Read-Host -Prompt 'Enter gateway secret (WHATSAPP_API_KEY)'
    }

    if (-not $secret) { Write-Error 'Secret empty, aborting'; exit 1 }

    Write-Host "Starting wa-gateway with secret from parent .env"
    $env:WA_GATEWAY_SECRET = $secret

    # Start gateway in background (use Start-Process to spawn and leave running)
    $node = (Get-Command node).Source
    if (-not $node) { Write-Error 'node not found in PATH'; exit 1 }

    # Use npm start to respect package.json script
    Start-Process -FilePath 'npm' -ArgumentList 'start' -WorkingDirectory $PSScriptRoot -NoNewWindow
    Write-Host 'Gateway started (check terminal where npm runs). Use Get-Process to find it.'
} finally {
    Pop-Location
}
