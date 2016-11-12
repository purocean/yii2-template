<?php
namespace console\controllers;

use common\models\User;
use console\models\SignupForm;

/**
 * Rbac manager.
 */
class RbacController extends \yii\console\Controller
{
    /**
     * Show Items.
     */
    public function actionIndex($username = null)
    {
        $auth = \Yii::$app->authManager;

        if (is_null($username)) {
            echo "- Roles:\n";
            foreach ($auth->getRoles() as $role) {
                echo "  {$role->name}\n";
            }

            echo "\n- Permissions:\n";
            foreach ($auth->getPermissions() as $permission) {
                echo "  {$permission->name}\n";
            }
        } else {
            $user = User::findByUsername($username);

            if ($user) {
                echo "- Roles:\n";
                foreach ($auth->getRolesByUser($user->id) as $role) {
                    echo "  {$role->name}\n";
                }

                echo "\n- Permissions:\n";
                foreach ($auth->getPermissionsByUser($user->id) as $permission) {
                    echo "  {$permission->name}\n";
                }
            } else {
                echo 'No such user.';
            }
        }

        return 0;
    }

    /**
     * Add Role.
     */
    public function actionAddRole($name)
    {
        $auth = \Yii::$app->authManager;
        $role = $auth->createRole($name);
        $auth->add($role);

        $this->actionIndex();
    }

    /**
     * Remove Role.
     */
    public function actionRemoveRole($name)
    {
        $auth = \Yii::$app->authManager;
        $role = $auth->getRole($name);
        $auth->remove($role);

        $this->actionIndex();
    }

    /**
     * Add Permission.
     */
    public function actionAddPermission($name)
    {
        $auth = \Yii::$app->authManager;
        $permission = $auth->createPermission($name);
        $auth->add($permission);

        $this->actionIndex();
    }

    /**
     * Remove Permission.
     */
    public function actionRemovePermission($name)
    {
        $auth = \Yii::$app->authManager;
        $permission = $auth->getPermission($name);
        $auth->remove($permission);

        $this->actionIndex();
    }

    /**
     * Show children.
     */
    public function actionShowChildren($itemName)
    {
        $auth = \Yii::$app->authManager;
        foreach ($auth->getChildren($itemName) as $key => $item) {
            echo "[{$item->type}] {$key}\n";
        }
    }

    /**
     * Add child.
     */
    public function actionAddChild($parentName, $childName)
    {
        $auth = \Yii::$app->authManager;

        $parent = $auth->getRole($parentName);
        $parent or $parent = $auth->getPermission($parentName);
        $child = $auth->getRole($childName);
        $child or $child = $auth->getPermission($childName);

        if ($auth->canAddChild($parent, $child)) {
            $auth->addChild($parent, $child);
        } else {
            echo "Cannot add child {$childName} to {$parentName}";
        }

        $this->actionShowChildren($parentName);
    }

    /**
     * Remove child.
     */
    public function actionRemoveChild($parentName, $childName)
    {
        $auth = \Yii::$app->authManager;

        $parent = $auth->getRole($parentName);
        $parent or $parent = $auth->getPermission($parentName);
        $child = $auth->getRole($childName);
        $child or $child = $auth->getPermission($childName);

        $auth->removeChild($parent, $child);

        $this->actionShowChildren($parentName);
    }

    /**
     * Add user.
     */
    public function actionAddUser($username, $password, $role = null, $email = null)
    {
        $model = new SignupForm();
        $model->username = $username;
        $model->password = $password;
        $model->email = $email;

        if ($model->signup()) {
            echo "Add user Success.\n";

            if (!is_null($role)) {
                $this->actionAssignRole($username, $role);
            }
        } else {
            foreach ($model->getErrors() as $key => $errors) {
                echo '['.$key.'] '.implode("\n", $errors)."\n";
            }
        }

        return 0;
    }

    /**
     * Reset user password.
     */
    public function actionResetPassword($username, $newPassword)
    {
        $user = User::findByUsername($username);
        $user->setPassword($newPassword);
        $user->removePasswordResetToken();

        if ($user->save(false)) {
            echo "[{$username}] new password is [{$newPassword}]\n";
        } else {
            echo "Reset password failed";
        }

        return 0;
    }

    /**
     * Assign role for user.
     */
    public function actionAssignRole($username, $role)
    {
        $user = User::findByUsername($username);
        $auth = \Yii::$app->authManager;
        $authorRole = $auth->getRole(mb_convert_encoding($role, 'UTF-8', 'ASCII,GB2312,GBK,UTF-8'));
        $auth->assign($authorRole, $user->getId());

        echo "Add role [{$role}] for [{$username}] success.\n";

        return 0;
    }

    /**
     * Revoke user role.
     */
    public function actionRevokeRole($username, $role)
    {
        $user = User::findByUsername($username);
        $auth = \Yii::$app->authManager;
        $authorRole = $auth->getRole(mb_convert_encoding($role, 'UTF-8', 'ASCII,GB2312,GBK,UTF-8'));
        $auth->revoke($authorRole, $user->getId());

        echo "remove role [{$role}] for [{$username}] success.\n";

        return 0;
    }

    /**
     * Assign permission for user.
     */
    public function actionAssignPermission($username, $permission)
    {
        $user = User::findByUsername($username);
        $auth = \Yii::$app->authManager;
        $authorPermission = $auth->getPermission(mb_convert_encoding($permission, 'UTF-8', 'ASCII,GB2312,GBK,UTF-8'));
        $auth->assign($authorPermission, $user->getId());

        echo "Add permission [{$permission}] for [{$username}] success.\n";

        return 0;
    }

    /**
     * Revoke user permission.
     */
    public function actionRevokePermission($username, $permission)
    {
        $user = User::findByUsername($username);
        $auth = \Yii::$app->authManager;
        $authorPermission = $auth->getPermission(mb_convert_encoding($permission, 'UTF-8', 'ASCII,GB2312,GBK,UTF-8'));
        $auth->revoke($authorPermission, $user->getId());

        echo "remove permission [{$permission}] for [{$username}] success.\n";

        return 0;
    }
}
