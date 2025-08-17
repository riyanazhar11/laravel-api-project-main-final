# Laravel API Testing Guide

## 🎯 Overview

This guide provides comprehensive testing setup for all refactored Laravel APIs using Postman and Newman CLI. The testing suite covers authentication, company management, and channel management endpoints with automated test cases.

## 📁 Files Structure

```
├── POSTMAN_TESTING_ROADMAP.md          # Detailed testing roadmap
├── POSTMAN_TEST_CASES.md               # All test cases for each endpoint
├── Laravel_API_Testing.postman_collection.json    # Postman collection
├── Laravel_API_Environment.postman_environment.json # Environment variables
├── newman-config.json                  # Newman CLI configuration
├── run-api-tests.sh                    # Linux/Mac test runner
├── run-api-tests.bat                   # Windows test runner
└── TESTING_README.md                   # This file
```

## 🚀 Quick Start

### Prerequisites

1. **Install Newman CLI:**
   ```bash
   npm install -g newman
   ```

2. **Start Laravel Server:**
   ```bash
   php artisan serve
   ```

3. **Run Tests:**
   ```bash
   # Linux/Mac
   chmod +x run-api-tests.sh
   ./run-api-tests.sh
   
   # Windows
   run-api-tests.bat
   ```

## 📋 Manual Testing with Postman

### 1. Import Collection and Environment

1. Open Postman
2. Click "Import" button
3. Import `Laravel_API_Testing.postman_collection.json`
4. Import `Laravel_API_Environment.postman_environment.json`
5. Select "Laravel API Environment" from dropdown

### 2. Add Test Cases

1. Open any request in the collection
2. Go to "Tests" tab
3. Copy test cases from `POSTMAN_TEST_CASES.md`
4. Paste into the Tests tab
5. Save the request

### 3. Run Tests

1. **Individual Request:** Click "Send" and check "Test Results" tab
2. **Entire Collection:** Right-click collection → "Run collection"
3. **Specific Folder:** Right-click folder → "Run folder"

## 🧪 Test Categories

### 1. Authentication Tests (7 endpoints)
- ✅ Register user
- ✅ Login user
- ✅ Verify email
- ✅ Resend verification
- ✅ Forgot password
- ✅ Reset password
- ✅ Logout

### 2. Company Management Tests (6 endpoints)
- ✅ Invite employee
- ✅ Accept invitation (GET)
- ✅ Accept invitation (POST)
- ✅ List invitations
- ✅ Cancel invitation
- ✅ Remove employee

### 3. Channel Management Tests (7 endpoints)
- ✅ Create channel
- ✅ List channels
- ✅ Get channel details
- ✅ Invite to channel
- ✅ Accept channel invitation
- ✅ Leave channel
- ✅ Delete channel

## 🔧 Test Features

### Automatic Token Management
- Login endpoint automatically sets `TOKEN` environment variable
- All authenticated requests use `Bearer {{TOKEN}}`
- Logout clears the token

### Environment Variable Management
- `USER_ID`: Current user ID
- `COMPANY_ID`: Current company ID
- `CHANNEL_ID`: Current channel ID
- `INVITATION_ID`: Current invitation ID
- `EMPLOYEE_ID`: Current employee ID

### Response Validation
- Status code verification
- Success flag validation
- Response structure validation
- Response time monitoring
- Data type validation

### Error Handling
- 422: Validation errors
- 401: Unauthorized access
- 403: Forbidden access
- 404: Resource not found
- 500: Server errors

## 📊 Test Reports

### Newman CLI Reports
- **CLI Report:** Console output with test results
- **JSON Report:** `test-results/test-results.json`
- **HTML Report:** `test-results/test-results.html`

### Postman Reports
- **Test Results Tab:** Individual request results
- **Collection Runner:** Summary of all tests
- **Console:** Detailed logs and errors

## 🎯 Testing Scenarios

### 1. Happy Path Testing
```bash
# Complete user journey
1. Register → Login → Verify Email
2. Create Company (auto-created)
3. Invite Employee → Accept Invitation
4. Create Channels → Invite Users
5. Test all operations
```

### 2. Error Path Testing
```bash
# Test error scenarios
1. Invalid credentials
2. Missing authentication
3. Insufficient permissions
4. Invalid data formats
5. Non-existent resources
```

### 3. Edge Case Testing
```bash
# Test edge cases
1. Duplicate invitations
2. Expired tokens
3. Boundary values
4. Concurrent requests
5. Large data sets
```

## 🔄 Continuous Integration

### GitHub Actions Example
```yaml
name: API Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup Node.js
        uses: actions/setup-node@v2
        with:
          node-version: '16'
      - name: Install Newman
        run: npm install -g newman
      - name: Run API Tests
        run: |
          newman run Laravel_API_Testing.postman_collection.json \
            -e Laravel_API_Environment.postman_environment.json \
            --config newman-config.json
```

### Local Development
```bash
# Run tests before commit
./run-api-tests.sh

# Run specific test category
newman run Laravel_API_Testing.postman_collection.json \
  -e Laravel_API_Environment.postman_environment.json \
  --folder "Authentication"
```

## 🛠️ Customization

### Adding New Test Cases
1. Copy template from `POSTMAN_TEST_CASES.md`
2. Modify for your specific endpoint
3. Add to Postman request
4. Update collection

### Modifying Environment Variables
1. Edit `Laravel_API_Environment.postman_environment.json`
2. Add new variables as needed
3. Update test scripts to use new variables

### Custom Test Scripts
```javascript
// Example custom test
pm.test("Custom validation", function () {
    var jsonData = pm.response.json();
    pm.expect(jsonData.custom_field).to.equal('expected_value');
});
```

## 📈 Performance Testing

### Response Time Benchmarks
- **Fast:** < 500ms
- **Acceptable:** < 1000ms
- **Slow:** < 2000ms
- **Unacceptable:** > 2000ms

### Load Testing
```bash
# Run with multiple iterations
newman run Laravel_API_Testing.postman_collection.json \
  -e Laravel_API_Environment.postman_environment.json \
  --iteration-count 100 \
  --delay-request 100
```

## 🔒 Security Testing

### Security Headers
- X-Content-Type-Options
- X-Frame-Options
- X-XSS-Protection
- Strict-Transport-Security

### Token Validation
- Token format validation
- Token expiration testing
- Token revocation testing

## 🐛 Troubleshooting

### Common Issues

1. **Newman not found:**
   ```bash
   npm install -g newman
   ```

2. **Collection import fails:**
   - Check JSON syntax
   - Verify file paths
   - Update Postman version

3. **Tests fail:**
   - Check Laravel server is running
   - Verify database migrations
   - Check environment variables

4. **Authentication fails:**
   - Verify token format
   - Check middleware configuration
   - Test with valid credentials

### Debug Mode
```bash
# Run with verbose output
newman run Laravel_API_Testing.postman_collection.json \
  -e Laravel_API_Environment.postman_environment.json \
  --verbose
```

## 📚 Additional Resources

- [Postman Documentation](https://learning.postman.com/)
- [Newman CLI Documentation](https://learning.postman.com/docs/running-collections/using-newman-cli/)
- [Laravel Testing Guide](https://laravel.com/docs/testing)
- [API Testing Best Practices](https://www.postman.com/use-cases/api-testing/)

## 🤝 Contributing

1. Add new test cases to `POSTMAN_TEST_CASES.md`
2. Update collection with new endpoints
3. Test thoroughly before committing
4. Update documentation

## 📞 Support

For issues or questions:
1. Check troubleshooting section
2. Review test logs
3. Verify API documentation
4. Test with minimal setup

---

**Happy Testing! 🎉**
