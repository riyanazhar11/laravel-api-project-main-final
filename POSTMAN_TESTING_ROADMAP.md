# Postman API Testing Roadmap

## Overview
This document provides a comprehensive testing roadmap for all refactored Laravel API endpoints using Postman. The roadmap includes request examples, expected responses, and testing scenarios.

## Base Configuration

### Environment Variables
Set up these environment variables in Postman:
```
BASE_URL: http://localhost:8000/api
TOKEN: (will be set after login)
```

### Headers
For authenticated requests, add:
```
Authorization: Bearer {{TOKEN}}
Content-Type: application/json
Accept: application/json
```

---

## 1. Authentication APIs

### 1.1 User Registration
**Endpoint:** `POST {{BASE_URL}}/signup`

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john.doe@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Expected Response (201):**
```json
{
    "success": true,
    "message": "User registered successfully. Company created. Please check your email to verify your account.",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john.doe@example.com",
            "email_verified": false,
            "company_id": 1
        },
        "company": {
            "id": 1,
            "name": "John Doe's Company",
            "owner_id": 1
        }
    }
}
```

**Test Cases:**
- ✅ Valid registration data
- ❌ Duplicate email
- ❌ Invalid email format
- ❌ Password mismatch
- ❌ Missing required fields

---

### 1.2 User Login
**Endpoint:** `POST {{BASE_URL}}/login`

**Request Body:**
```json
{
    "email": "john.doe@example.com",
    "password": "password123"
}
```

**Expected Response (200):**
```json
{
    "success": true,
    "message": "Login successful",
    "token": "60|abc123def456...",
    "email_verified": true
}
```

**Test Cases:**
- ✅ Valid credentials
- ❌ Invalid email
- ❌ Wrong password
- ❌ Unverified email (should return 403)

---

### 1.3 Email Verification
**Endpoint:** `GET {{BASE_URL}}/verify-email/{token}`

**Request:** Replace `{token}` with the verification token from email

**Expected Response (200):**
```json
{
    "success": true,
    "message": "Email verified successfully"
}
```

**Test Cases:**
- ✅ Valid token
- ❌ Invalid token
- ❌ Expired token

---

### 1.4 Resend Verification Email
**Endpoint:** `POST {{BASE_URL}}/resend-verification`

**Headers:** `Authorization: Bearer {{TOKEN}}`

**Expected Response (200):**
```json
{
    "success": true,
    "message": "Verification email sent successfully"
}
```

**Test Cases:**
- ✅ Authenticated user
- ❌ Unauthenticated user
- ❌ Already verified email

---

### 1.5 Forgot Password
**Endpoint:** `POST {{BASE_URL}}/forgot-password`

**Request Body:**
```json
{
    "email": "john.doe@example.com"
}
```

**Expected Response (200):**
```json
{
    "success": true,
    "message": "Password reset email sent."
}
```

**Test Cases:**
- ✅ Valid email
- ❌ Invalid email format
- ❌ Non-existent email

---

### 1.6 Reset Password
**Endpoint:** `POST {{BASE_URL}}/reset-password`

**Request Body:**
```json
{
    "token": "reset_token_from_email",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

**Expected Response (200):**
```json
{
    "success": true,
    "message": "Password has been reset successfully."
}
```

**Test Cases:**
- ✅ Valid token and password
- ❌ Invalid token
- ❌ Password mismatch
- ❌ Weak password

---

### 1.7 Logout
**Endpoint:** `POST {{BASE_URL}}/logout`

**Headers:** `Authorization: Bearer {{TOKEN}}`

**Expected Response (200):**
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

**Test Cases:**
- ✅ Valid token
- ❌ Invalid token
- ❌ Missing token

---

## 2. Company Management APIs

### 2.1 Invite Employee
**Endpoint:** `POST {{BASE_URL}}/invite-employee`

**Headers:** `Authorization: Bearer {{TOKEN}}`

**Request Body:**
```json
{
    "name": "Jane Smith",
    "email": "jane.smith@example.com"
}
```

**Expected Response (200):**
```json
{
    "success": true,
    "message": "Invitation sent successfully",
    "data": {
        "invitation_id": 1,
        "email": "jane.smith@example.com",
        "expires_at": "2024-01-20T10:00:00.000000Z"
    }
}
```

**Test Cases:**
- ✅ Company owner invites employee
- ❌ Non-owner tries to invite (403)
- ❌ Duplicate invitation
- ❌ Invalid email format

---

### 2.2 Accept Company Invitation (GET - Auto-accept)
**Endpoint:** `GET {{BASE_URL}}/accept-invitation/{token}`

**Expected Response (200):**
```json
{
    "success": true,
    "message": "Invitation accepted successfully! You are now an employee of John Doe's Company. Please check your email for login credentials.",
    "data": {
        "user": {
            "id": 2,
            "name": "Jane Smith",
            "email": "jane.smith@example.com",
            "company_id": 1
        },
        "token": "60|xyz789abc123...",
        "password": "auto_generated_password"
    }
}
```

**Test Cases:**
- ✅ Valid invitation token
- ❌ Invalid token
- ❌ Expired invitation
- ❌ Already accepted invitation

---

### 2.3 Accept Company Invitation (POST - Custom Password)
**Endpoint:** `POST {{BASE_URL}}/accept-invitation/{token}`

**Request Body:**
```json
{
    "password": "custompassword123",
    "password_confirmation": "custompassword123"
}
```

**Expected Response (200):**
```json
{
    "success": true,
    "message": "Invitation accepted successfully. You are now an employee of John Doe's Company",
    "data": {
        "user": {
            "id": 2,
            "name": "Jane Smith",
            "email": "jane.smith@example.com",
            "company_id": 1
        },
        "token": "60|xyz789abc123..."
    }
}
```

---

### 2.4 List Pending Invitations
**Endpoint:** `GET {{BASE_URL}}/invitations`

**Headers:** `Authorization: Bearer {{TOKEN}}`

**Expected Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "company_id": 1,
            "email": "jane.smith@example.com",
            "name": "Jane Smith",
            "invitation_token": "abc123...",
            "status": "pending",
            "expires_at": "2024-01-20T10:00:00.000000Z",
            "created_at": "2024-01-13T10:00:00.000000Z"
        }
    ]
}
```

**Test Cases:**
- ✅ Company owner views invitations
- ❌ Non-owner tries to view (403)

---

### 2.5 Cancel Invitation
**Endpoint:** `DELETE {{BASE_URL}}/invitations/{invitation_id}`

**Headers:** `Authorization: Bearer {{TOKEN}}`

**Expected Response (200):**
```json
{
    "success": true,
    "message": "Invitation cancelled successfully"
}
```

**Test Cases:**
- ✅ Company owner cancels invitation
- ❌ Non-owner tries to cancel (403)
- ❌ Invalid invitation ID

---

### 2.6 Remove Employee
**Endpoint:** `DELETE {{BASE_URL}}/employees/{employee_id}`

**Headers:** `Authorization: Bearer {{TOKEN}}`

**Expected Response (200):**
```json
{
    "success": true,
    "message": "Employee removed successfully"
}
```

**Test Cases:**
- ✅ Company owner removes employee
- ❌ Non-owner tries to remove (403)
- ❌ Invalid employee ID
- ❌ Try to remove owner (should fail)

---

## 3. Channel Management APIs

### 3.1 Create Channel
**Endpoint:** `POST {{BASE_URL}}/channels`

**Headers:** `Authorization: Bearer {{TOKEN}}`

**Request Body:**
```json
{
    "name": "General Discussion",
    "description": "General company discussions",
    "type": "public"
}
```

**Expected Response (201):**
```json
{
    "success": true,
    "message": "Channel created successfully",
    "data": {
        "channel": {
            "id": 1,
            "name": "General Discussion",
            "description": "General company discussions",
            "type": "public",
            "created_by": "John Doe",
            "created_at": "2024-01-13T10:00:00.000000Z"
        }
    }
}
```

**Test Cases:**
- ✅ Create public channel
- ✅ Create private channel
- ❌ Invalid channel type
- ❌ Missing required fields

---

### 3.2 List Channels
**Endpoint:** `GET {{BASE_URL}}/channels`

**Headers:** `Authorization: Bearer {{TOKEN}}`

**Expected Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "General Discussion",
            "description": "General company discussions",
            "type": "public",
            "created_by": "John Doe",
            "is_member": true,
            "member_count": 1,
            "created_at": "2024-01-13T10:00:00.000000Z"
        }
    ]
}
```

**Test Cases:**
- ✅ Authenticated user views channels
- ❌ Unauthenticated user (401)

---

### 3.3 Get Channel Details
**Endpoint:** `GET {{BASE_URL}}/channels/{channel_id}`

**Headers:** `Authorization: Bearer {{TOKEN}}`

**Expected Response (200):**
```json
{
    "success": true,
    "data": {
        "channel": {
            "id": 1,
            "name": "General Discussion",
            "description": "General company discussions",
            "type": "public",
            "created_by": "John Doe",
            "is_member": true,
            "member_count": 1,
            "created_at": "2024-01-13T10:00:00.000000Z"
        },
        "members": [
            {
                "id": 1,
                "name": "John Doe",
                "email": "john.doe@example.com",
                "role": "admin",
                "joined_at": "2024-01-13T10:00:00.000000Z"
            }
        ]
    }
}
```

**Test Cases:**
- ✅ Channel member views details
- ❌ Non-member tries to view private channel (403)
- ❌ Invalid channel ID (404)

---

### 3.4 Invite User to Channel
**Endpoint:** `POST {{BASE_URL}}/channels/{channel_id}/invite`

**Headers:** `Authorization: Bearer {{TOKEN}}`

**Request Body:**
```json
{
    "user_id": 2
}
```

**Expected Response (200):**
```json
{
    "success": true,
    "message": "Invitation sent successfully",
    "data": {
        "invitation_id": 1,
        "invited_user": {
            "id": 2,
            "name": "Jane Smith",
            "email": "jane.smith@example.com"
        },
        "expires_at": "2024-01-20T10:00:00.000000Z"
    }
}
```

**Test Cases:**
- ✅ Channel member invites user
- ❌ Non-member tries to invite (403)
- ❌ User already member
- ❌ Invalid user ID

---

### 3.5 Accept Channel Invitation
**Endpoint:** `GET {{BASE_URL}}/accept-channel-invitation/{token}`

**Expected Response (200):**
```json
{
    "success": true,
    "message": "You have successfully joined General Discussion",
    "data": {
        "channel": {
            "id": 1,
            "name": "General Discussion",
            "type": "public"
        }
    }
}
```

**Test Cases:**
- ✅ Valid invitation token
- ❌ Invalid token
- ❌ Expired invitation
- ❌ Already member

---

### 3.6 Leave Channel
**Endpoint:** `DELETE {{BASE_URL}}/channels/{channel_id}/leave`

**Headers:** `Authorization: Bearer {{TOKEN}}`

**Expected Response (200):**
```json
{
    "success": true,
    "message": "You have left the channel successfully"
}
```

**Test Cases:**
- ✅ Channel member leaves
- ❌ Non-member tries to leave (403)
- ❌ Channel creator tries to leave (should fail)

---

### 3.7 Delete Channel
**Endpoint:** `DELETE {{BASE_URL}}/channels/{channel_id}`

**Headers:** `Authorization: Bearer {{TOKEN}}`

**Expected Response (200):**
```json
{
    "success": true,
    "message": "Channel deleted successfully"
}
```

**Test Cases:**
- ✅ Channel creator deletes channel
- ❌ Non-creator tries to delete (403)
- ❌ Invalid channel ID (404)

---

## 4. Testing Scenarios

### 4.1 Complete User Journey
1. Register new user
2. Verify email
3. Login and get token
4. Create company (auto-created during registration)
5. Invite employee
6. Employee accepts invitation
7. Create channels
8. Invite users to channels
9. Test channel operations

### 4.2 Authorization Testing
1. Test all endpoints without authentication
2. Test company owner vs employee permissions
3. Test channel member vs non-member permissions
4. Test channel admin vs regular member permissions

### 4.3 Error Handling Testing
1. Test all validation errors
2. Test authorization errors (403)
3. Test not found errors (404)
4. Test server errors (500)

### 4.4 Edge Cases
1. Duplicate invitations
2. Expired tokens
3. Invalid data formats
4. Missing required fields
5. Boundary value testing

---

## 5. Postman Collection Setup

### 5.1 Create Collection
1. Create new collection: "Laravel API Testing"
2. Set base URL: `{{BASE_URL}}`
3. Add environment variables

### 5.2 Organize Folders
```
Laravel API Testing/
├── Authentication/
│   ├── Register
│   ├── Login
│   ├── Verify Email
│   ├── Resend Verification
│   ├── Forgot Password
│   ├── Reset Password
│   └── Logout
├── Company Management/
│   ├── Invite Employee
│   ├── Accept Invitation (GET)
│   ├── Accept Invitation (POST)
│   ├── List Invitations
│   ├── Cancel Invitation
│   └── Remove Employee
└── Channel Management/
    ├── Create Channel
    ├── List Channels
    ├── Get Channel Details
    ├── Invite to Channel
    ├── Accept Channel Invitation
    ├── Leave Channel
    └── Delete Channel
```

### 5.3 Pre-request Scripts
For login endpoint, add this script to automatically set the token:
```javascript
pm.test("Set token", function () {
    var jsonData = pm.response.json();
    if (jsonData.token) {
        pm.environment.set("TOKEN", jsonData.token);
    }
});
```

---

## 6. Performance Testing

### 6.1 Load Testing
- Test with multiple concurrent users
- Monitor response times
- Check for memory leaks
- Test database performance

### 6.2 Security Testing
- Test SQL injection attempts
- Test XSS attempts
- Test CSRF protection
- Test rate limiting

---

## 7. Documentation

### 7.1 API Documentation
- Export Postman collection
- Generate API documentation
- Include all request/response examples
- Document error codes and messages

### 7.2 Testing Report
- Document all test cases
- Record test results
- Note any bugs found
- Track performance metrics

---

## 8. Automation

### 8.1 Newman CLI
Run tests via command line:
```bash
newman run Laravel_API_Testing.postman_collection.json -e environment.json
```

### 8.2 CI/CD Integration
- Integrate with GitHub Actions
- Run tests on every push
- Generate test reports
- Notify on failures

---

## Conclusion

This roadmap provides a comprehensive testing strategy for all refactored APIs. Follow the testing scenarios systematically to ensure all functionality works correctly and the refactoring maintains backward compatibility while improving code quality.
