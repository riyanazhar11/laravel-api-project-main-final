@echo off
REM Laravel API Test Runner for Windows
REM This script runs comprehensive tests for all refactored APIs

echo 🚀 Starting Laravel API Test Suite
echo ==================================

REM Check if Newman is installed
newman --version >nul 2>&1
if errorlevel 1 (
    echo ❌ Newman is not installed. Please install it first:
    echo npm install -g newman
    pause
    exit /b 1
)

REM Check if required files exist
if not exist "Laravel_API_Testing.postman_collection.json" (
    echo ❌ Postman collection file not found!
    pause
    exit /b 1
)

if not exist "Laravel_API_Environment.postman_environment.json" (
    echo ❌ Postman environment file not found!
    pause
    exit /b 1
)

REM Create test results directory
if not exist "test-results" mkdir test-results

echo 📋 Running Authentication Tests...
newman run Laravel_API_Testing.postman_collection.json -e Laravel_API_Environment.postman_environment.json --folder "Authentication" --config newman-config.json --export-environment test-results/auth-environment.json

echo 📋 Running Company Management Tests...
newman run Laravel_API_Testing.postman_collection.json -e test-results/auth-environment.json --folder "Company Management" --config newman-config.json --export-environment test-results/company-environment.json

echo 📋 Running Channel Management Tests...
newman run Laravel_API_Testing.postman_collection.json -e test-results/company-environment.json --folder "Channel Management" --config newman-config.json --export-environment test-results/channel-environment.json

echo 📋 Running Complete Test Suite...
newman run Laravel_API_Testing.postman_collection.json -e Laravel_API_Environment.postman_environment.json --config newman-config.json --export-environment test-results/final-environment.json

echo ✅ Test Suite Completed!
echo 📊 Check test-results/ for detailed reports
echo 📄 HTML Report: test-results/test-results.html
echo 📄 JSON Report: test-results/test-results.json

echo.
echo 🎯 Next Steps:
echo 1. Review test results in test-results/
echo 2. Fix any failing tests
echo 3. Update test cases as needed
echo 4. Run tests again to verify fixes

pause
