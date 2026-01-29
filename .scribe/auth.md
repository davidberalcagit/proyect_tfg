# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer e.g. 1|laravel_sanctum_token..."`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

You can retrieve your token by logging in via <b>POST /api/login</b>.
