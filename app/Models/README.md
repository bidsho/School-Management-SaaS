# School Management SaaS Eloquent Models

Contains 29 model PHP files plus the `BelongsToSchool` trait, aligned to the 30 migration tables previously outlined.

## Install
Copy `app/Models` into your Laravel project. Preserve your existing `User.php`; follow `USER_MODEL_PATCH.txt` to add the relationships.

## Important
- The `parents` database table uses the PHP model `Guardian` because `Parent` is a reserved PHP keyword.
- The tenant trait's `forSchool($schoolId)` scope is opt-in. It does not automatically enforce tenant isolation. Enforce tenant restrictions in policies, services and query paths.
- Ensure model fillable fields match your migrations.
