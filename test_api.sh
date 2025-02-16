#!/bin/bash

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Base URL
API_URL="http://localhost:8000"

# Function to print section header
print_header() {
    echo -e "\n${BLUE}=== $1 ===${NC}\n"
}

# Function to check response
check_response() {
    if [[ $1 == 2* ]]; then
        echo -e "${GREEN}Success (Status: $1)${NC}"
    else
        echo -e "${RED}Failed (Status: $1)${NC}"
        echo "Response: $2"
    fi
}

# Store token
TOKEN=""

# 1. Login as admin to get token
print_header "Logging in"
RESPONSE=$(curl -v -s -w "\n%{http_code}" -X POST "$API_URL/api/login" \
    -H "Content-Type: application/json" \
    -d '{
        "email": "sysadmin@id-ingenierie.com",
        "password": "ID#Admin2025@Secure"
    }')
HTTP_STATUS=$(echo "$RESPONSE" | tail -n1)
RESPONSE_BODY=$(echo "$RESPONSE" | sed '$d')
check_response $HTTP_STATUS "$RESPONSE_BODY"

# Extract token from response
TOKEN=$(echo $RESPONSE_BODY | grep -o '"token":"[^"]*' | cut -d'"' -f4)
if [ -z "$TOKEN" ]; then
    echo -e "${RED}Failed to get token. Exiting.${NC}"
    exit 1
fi
echo -e "${GREEN}Token obtained successfully${NC}"

# Test Permissions API
test_permissions() {
    print_header "Testing Permissions API"

    # List permissions
    echo "Getting all permissions..."
    RESPONSE=$(curl -s -w "\n%{http_code}" -X GET "$API_URL/api/permissions" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Content-Type: application/json")
    HTTP_STATUS=$(echo "$RESPONSE" | tail -n1)
    RESPONSE_BODY=$(echo "$RESPONSE" | sed '$d')
    check_response $HTTP_STATUS "$RESPONSE_BODY"

    # Create permission
    echo -e "\nCreating new permission..."
    RESPONSE=$(curl -s -w "\n%{http_code}" -X POST "$API_URL/api/permissions" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Content-Type: application/json" \
        -d '{"name": "test_permission"}')
    HTTP_STATUS=$(echo "$RESPONSE" | tail -n1)
    RESPONSE_BODY=$(echo "$RESPONSE" | sed '$d')
    check_response $HTTP_STATUS "$RESPONSE_BODY"

    # Get permission ID from response
    PERMISSION_ID=$(echo $RESPONSE_BODY | grep -o '"id":[0-9]*' | cut -d':' -f2)

    # Get specific permission
    echo -e "\nGetting specific permission..."
    RESPONSE=$(curl -s -w "\n%{http_code}" -X GET "$API_URL/api/permissions/$PERMISSION_ID" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Content-Type: application/json")
    HTTP_STATUS=$(echo "$RESPONSE" | tail -n1)
    RESPONSE_BODY=$(echo "$RESPONSE" | sed '$d')
    check_response $HTTP_STATUS "$RESPONSE_BODY"

    # Update permission
    echo -e "\nUpdating permission..."
    RESPONSE=$(curl -s -w "\n%{http_code}" -X PUT "$API_URL/api/permissions/$PERMISSION_ID" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Content-Type: application/json" \
        -d '{"name": "updated_test_permission"}')
    HTTP_STATUS=$(echo "$RESPONSE" | tail -n1)
    RESPONSE_BODY=$(echo "$RESPONSE" | sed '$d')
    check_response $HTTP_STATUS "$RESPONSE_BODY"

    # Delete permission
    echo -e "\nDeleting permission..."
    RESPONSE=$(curl -s -w "\n%{http_code}" -X DELETE "$API_URL/api/permissions/$PERMISSION_ID" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Content-Type: application/json")
    HTTP_STATUS=$(echo "$RESPONSE" | tail -n1)
    RESPONSE_BODY=$(echo "$RESPONSE" | sed '$d')
    check_response $HTTP_STATUS "$RESPONSE_BODY"
}

# Test Roles API
test_roles() {
    print_header "Testing Roles API"

    # List roles
    echo "Getting all roles..."
    RESPONSE=$(curl -s -w "\n%{http_code}" -X GET "$API_URL/api/roles" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Content-Type: application/json")
    HTTP_STATUS=$(echo "$RESPONSE" | tail -n1)
    RESPONSE_BODY=$(echo "$RESPONSE" | sed '$d')
    check_response $HTTP_STATUS "$RESPONSE_BODY"

    # Create role
    echo -e "\nCreating new role..."
    RESPONSE=$(curl -s -w "\n%{http_code}" -X POST "$API_URL/api/roles" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Content-Type: application/json" \
        -d '{
            "name": "test_role",
            "permissions": ["test_permission"]
        }')
    HTTP_STATUS=$(echo "$RESPONSE" | tail -n1)
    RESPONSE_BODY=$(echo "$RESPONSE" | sed '$d')
    check_response $HTTP_STATUS "$RESPONSE_BODY"

    # Get role ID from response
    ROLE_ID=$(echo $RESPONSE_BODY | grep -o '"id":[0-9]*' | cut -d':' -f2)

    # Get specific role
    echo -e "\nGetting specific role..."
    RESPONSE=$(curl -s -w "\n%{http_code}" -X GET "$API_URL/api/roles/$ROLE_ID" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Content-Type: application/json")
    HTTP_STATUS=$(echo "$RESPONSE" | tail -n1)
    RESPONSE_BODY=$(echo "$RESPONSE" | sed '$d')
    check_response $HTTP_STATUS "$RESPONSE_BODY"

    # Update role
    echo -e "\nUpdating role..."
    RESPONSE=$(curl -s -w "\n%{http_code}" -X PUT "$API_URL/api/roles/$ROLE_ID" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Content-Type: application/json" \
        -d '{
            "name": "updated_test_role",
            "permissions": ["test_permission", "another_permission"]
        }')
    HTTP_STATUS=$(echo "$RESPONSE" | tail -n1)
    RESPONSE_BODY=$(echo "$RESPONSE" | sed '$d')
    check_response $HTTP_STATUS "$RESPONSE_BODY"

    # Delete role
    echo -e "\nDeleting role..."
    RESPONSE=$(curl -s -w "\n%{http_code}" -X DELETE "$API_URL/api/roles/$ROLE_ID" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Content-Type: application/json")
    HTTP_STATUS=$(echo "$RESPONSE" | tail -n1)
    RESPONSE_BODY=$(echo "$RESPONSE" | sed '$d')
    check_response $HTTP_STATUS "$RESPONSE_BODY"
}

# Run tests
test_permissions
test_roles

# Logout
print_header "Logging out"
RESPONSE=$(curl -s -w "\n%{http_code}" -X POST "$API_URL/api/logout" \
    -H "Authorization: Bearer $TOKEN" \
    -H "Content-Type: application/json")
HTTP_STATUS=$(echo "$RESPONSE" | tail -n1)
RESPONSE_BODY=$(echo "$RESPONSE" | sed '$d')
check_response $HTTP_STATUS "$RESPONSE_BODY"

print_header "Testing completed"
