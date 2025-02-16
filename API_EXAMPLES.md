# API Examples

## Authentication

First, you need to get an authentication token. You can either register a new user or login with existing credentials.

### Register a new user
```bash
curl -X POST http://localhost/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### Login
```bash
curl -X POST http://localhost/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123",
    "device_name": "test_device"
  }'
```

The response will include a token. Use this token in the Authorization header for all subsequent requests:
```bash
export TOKEN="your_token_here"
```

## Roles Management

### List all roles
```bash
curl -X GET http://localhost/api/roles \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json"
```

### Create a new role
```bash
curl -X POST http://localhost/api/roles \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "editor",
    "permissions": ["edit_posts", "delete_posts"]
  }'
```

### Get a specific role
```bash
curl -X GET http://localhost/api/roles/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json"
```

### Update a role
```bash
curl -X PUT http://localhost/api/roles/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "senior_editor",
    "permissions": ["edit_posts", "delete_posts", "publish_posts"]
  }'
```

### Delete a role
```bash
curl -X DELETE http://localhost/api/roles/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json"
```

## Permissions Management

### List all permissions
```bash
curl -X GET http://localhost/api/permissions \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json"
```

### Create a new permission
```bash
curl -X POST http://localhost/api/permissions \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "publish_posts"
  }'
```

### Get a specific permission
```bash
curl -X GET http://localhost/api/permissions/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json"
```

### Update a permission
```bash
curl -X PUT http://localhost/api/permissions/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "manage_posts"
  }'
```

### Delete a permission
```bash
curl -X DELETE http://localhost/api/permissions/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json"
```

## Example Workflow

Here's a complete workflow example:

1. Create required permissions:
```bash
# Create edit_posts permission
curl -X POST http://localhost/api/permissions \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name": "edit_posts"}'

# Create delete_posts permission
curl -X POST http://localhost/api/permissions \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name": "delete_posts"}'

# Create publish_posts permission
curl -X POST http://localhost/api/permissions \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name": "publish_posts"}'
```

2. Create an editor role with specific permissions:
```bash
curl -X POST http://localhost/api/roles \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "editor",
    "permissions": ["edit_posts", "delete_posts"]
  }'
```

3. Update the role to add more permissions:
```bash
curl -X PUT http://localhost/api/roles/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "editor",
    "permissions": ["edit_posts", "delete_posts", "publish_posts"]
  }'
```

## Response Examples

### Successful role creation
```json
{
    "name": "editor",
    "guard_name": "web",
    "updated_at": "2025-02-14T15:11:39.000000Z",
    "created_at": "2025-02-14T15:11:39.000000Z",
    "id": 1,
    "permissions": [
        {
            "id": 1,
            "name": "edit_posts",
            "guard_name": "web"
        },
        {
            "id": 2,
            "name": "delete_posts",
            "guard_name": "web"
        }
    ]
}
```

### Error response (unauthorized)
```json
{
    "message": "Unauthenticated."
}
```

### Error response (validation)
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "name": [
            "The name has already been taken."
        ]
    }
}
```

## Notes

1. Replace `http://localhost` with your actual API base URL
2. The `$TOKEN` variable should be set to the token received from login/register
3. All requests must include:
   - `Authorization: Bearer $TOKEN` header
   - `Content-Type: application/json` header
4. The user must have appropriate permissions to access these endpoints
5. All responses are in JSON format
