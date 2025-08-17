#!/bin/bash

# Laravel API Test Runner
# This script runs comprehensive tests for all refactored APIs

echo "🚀 Starting Laravel API Test Suite"
echo "=================================="

# Check if Newman is installed
if ! command -v newman &> /dev/null; then
    echo "❌ Newman is not installed. Please install it first:"
    echo "npm install -g newman"
    exit 1
fi

# Check if required files exist
if [ ! -f "Laravel_API_Testing.postman_collection.json" ]; then
    echo "❌ Postman collection file not found!"
    exit 1
fi

if [ ! -f "Laravel_API_Environment.postman_environment.json" ]; then
    echo "❌ Postman environment file not found!"
    exit 1
fi

# Create test results directory
mkdir -p test-results

echo "📋 Running Authentication Tests..."
newman run Laravel_API_Testing.postman_collection.json \
    -e Laravel_API_Environment.postman_environment.json \
    --folder "Authentication" \
    --config newman-config.json \
    --export-environment test-results/auth-environment.json

echo "📋 Running Company Management Tests..."
newman run Laravel_API_Testing.postman_collection.json \
    -e test-results/auth-environment.json \
    --folder "Company Management" \
    --config newman-config.json \
    --export-environment test-results/company-environment.json

echo "📋 Running Channel Management Tests..."
newman run Laravel_API_Testing.postman_collection.json \
    -e test-results/company-environment.json \
    --folder "Channel Management" \
    --config newman-config.json \
    --export-environment test-results/channel-environment.json

echo "📋 Running Complete Test Suite..."
newman run Laravel_API_Testing.postman_collection.json \
    -e Laravel_API_Environment.postman_environment.json \
    --config newman-config.json \
    --export-environment test-results/final-environment.json

echo "✅ Test Suite Completed!"
echo "📊 Check test-results/ for detailed reports"
echo "📄 HTML Report: test-results/test-results.html"
echo "📄 JSON Report: test-results/test-results.json"

# Display summary
if [ -f "test-results/test-results.json" ]; then
    echo ""
    echo "📈 Test Summary:"
    echo "================"
    jq '.run.stats' test-results/test-results.json 2>/dev/null || echo "Install jq for detailed summary"
fi

echo ""
echo "🎯 Next Steps:"
echo "1. Review test results in test-results/"
echo "2. Fix any failing tests"
echo "3. Update test cases as needed"
echo "4. Run tests again to verify fixes"
