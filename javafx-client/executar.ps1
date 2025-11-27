# Script para executar o aplicativo JavaFX
# Este script detecta se Maven está instalado e usa o Maven Wrapper se necessário

$env:Path = [System.Environment]::GetEnvironmentVariable("Path","Machine") + ";" + [System.Environment]::GetEnvironmentVariable("Path","User")

# Função para verificar se um comando existe
function Test-Command {
    param($Command)
    $null = Get-Command $Command -ErrorAction SilentlyContinue
    return $?
}

# Determinar qual comando Maven usar
$mvnCommand = $null

# Verificar se Maven está instalado globalmente
if (Test-Command "mvn") {
    Write-Host "Maven encontrado no sistema. Usando Maven global." -ForegroundColor Cyan
    $mvnCommand = "mvn"
}
# Verificar se Maven Wrapper existe
elseif (Test-Path ".\mvnw.cmd") {
    Write-Host "Maven não encontrado. Usando Maven Wrapper (mvnw)." -ForegroundColor Yellow
    $mvnCommand = ".\mvnw.cmd"
}
# Se não tem nem Maven nem wrapper, tentar gerar o wrapper
else {
    Write-Host "Maven não encontrado e Maven Wrapper não existe." -ForegroundColor Yellow
    Write-Host "Tentando gerar Maven Wrapper..." -ForegroundColor Yellow
    
    # Verificar se Java está instalado (necessário para gerar wrapper)
    if (-not (Test-Command "java")) {
        Write-Host "ERRO: Java não está instalado ou não está no PATH!" -ForegroundColor Red
        Write-Host "Por favor, instale Java 17 ou superior e adicione ao PATH." -ForegroundColor Red
        Write-Host "Ou instale Maven: https://maven.apache.org/download.cgi" -ForegroundColor Yellow
        exit 1
    }
    
    # Tentar usar Maven para gerar wrapper (se estiver em algum lugar não padrão)
    # Se não conseguir, fornecer instruções
    Write-Host "Para gerar o Maven Wrapper, você precisa:" -ForegroundColor Yellow
    Write-Host "1. Instalar Maven: https://maven.apache.org/download.cgi" -ForegroundColor Yellow
    Write-Host "2. Ou executar: mvn wrapper:wrapper" -ForegroundColor Yellow
    Write-Host "" -ForegroundColor Yellow
    Write-Host "Alternativamente, você pode instalar Maven e executar:" -ForegroundColor Yellow
    Write-Host "  mvn clean javafx:run" -ForegroundColor Cyan
    exit 1
}

# Compilar o projeto
Write-Host "Compilando projeto..." -ForegroundColor Yellow
& $mvnCommand clean compile -q

if ($LASTEXITCODE -ne 0) {
    Write-Host "Erro na compilação!" -ForegroundColor Red
    exit 1
}

# Executar o aplicativo JavaFX
Write-Host "Executando aplicativo JavaFX..." -ForegroundColor Green
& $mvnCommand javafx:run

