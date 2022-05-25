<?php

namespace App\Http\Backoffice;

final class Permission
{
    public const PROTECTED_USER = 1;
    public const PROTECTED_ROLE = 1;

    public const ROLE_LIST = 'backoffice.roles.list';
    public const ROLE_READ = 'backoffice.roles.read';
    public const ROLE_CREATE = 'backoffice.roles.create';
    public const ROLE_UPDATE = 'backoffice.roles.update';
    public const ROLE_DELETE = 'backoffice.roles.delete';
    public const ROLE_EXPORT = 'backoffice.roles.export';

    public const OPERATOR_LIST = 'backoffice.operators.list';
    public const OPERATOR_READ = 'backoffice.operators.read';
    public const OPERATOR_CREATE = 'backoffice.operators.create';
    public const OPERATOR_UPDATE = 'backoffice.operators.update';
    public const OPERATOR_DELETE = 'backoffice.operators.delete';
    public const OPERATOR_EXPORT = 'backoffice.operators.export';
    public const OPERATOR_RESET_PASSWORD = 'backoffice.operators.reset-password';
    public const OPERATOR_RESEND_ACTIVATION = 'backoffice.operators.resend-activation';

    public const GUEST_CATEGORY_LIST = 'backoffice.guest_categories.list';
    public const GUEST_CATEGORY_CREATE = 'backoffice.guest_categories.create';
    public const GUEST_CATEGORY_EDIT = 'backoffice.guest_categories.edit';
    public const GUEST_CATEGORY_SHOW = 'backoffice.guest_categories.show';
    public const GUEST_CATEGORY_DELETE = 'backoffice.guest_categories.delete';

    public const GUEST_USER_LIST = 'backoffice.guest_users.list';
    public const GUEST_USER_CREATE = 'backoffice.guest_users.create';
    public const GUEST_USER_EDIT = 'backoffice.guest_users.edit';
    public const GUEST_USER_SHOW = 'backoffice.guest_users.show';
    public const GUEST_USER_DELETE = 'backoffice.guest_users.delete';
}
