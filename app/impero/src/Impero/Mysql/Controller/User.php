<?php namespace Impero\Mysql\Controller;

use Pckg\Maestro\Helper\Maestro;
use Impero\Mysql\Entity\Users;
use Impero\Mysql\Form\User as UserForm;
use Impero\Mysql\Record\User as UserRecord;
use Pckg\Framework\Controller;

class User extends Controller
{

    use Maestro;

    /**
     * List all available users.
     *
     * @param Users $users
     * @return mixed
     */
    public function getIndexAction(Users $users)
    {
        return $this->tabelize($users, ['name'], 'Users');
    }

    /**
     * Show add form.
     * Form is automatically filled with session data.
     *
     * @param UserForm   $userForm
     * @param UserRecord $userRecord
     * @return mixed
     */
    public function getAddAction(UserForm $userForm, UserRecord $userRecord)
    {
        /**
         * We may have some default values.
         */
        $userForm->populateFromRecord($userRecord);

        /**
         * Then we simply display form.
         */
        return $this->formalize($userForm, $userRecord, 'Add user');
    }

    /**
     * Save form data.
     * Form is automatically filled with request data.
     *
     * @param UserForm   $userForm
     * @param UserRecord $userRecord
     * @return mixed
     */
    public function postAddAction(UserForm $userForm, UserRecord $userRecord)
    {
        /**
         * Fill record with posted data.
         */
        $userForm->populateToRecord($userRecord);

        /**
         * User ID may be set if admin added it.
         */
        $userRecord->setUserIdByAuthIfNotSet();

        /**
         * Simply save record.
         */
        $userRecord->save();

        /**
         * If user was added via ajax, we display some data and redirect url.
         * Otherwise we redirect user to edit form.
         */
        return $this->response()->respondWithSuccessRedirect($userRecord->getEditUrl());
    }

    /**
     * Show edit form.
     * Form is automatically filled with session data.
     *
     * @param UserForm   $userForm
     * @param UserRecord $userRecord
     * @return mixed
     */
    public function getEditAction(UserForm $userForm, UserRecord $userRecord)
    {
        /**
         * Fill form with record data.
         */
        $userForm->populateFromRecord($userRecord);

        /**
         * Then we simply display form.
         */
        return $this->formalize($userForm, $userRecord, 'Edit user');
    }

    /**
     * Save form data.
     * Form was automatically filled with request data.
     *
     * @param UserForm   $userForm
     * @param UserRecord $userRecord
     * @return $this
     */
    public function postEditAction(UserForm $userForm, UserRecord $userRecord)
    {
        /**
         * Fill record with posted data.
         */
        $userForm->populateToRecordAndSave($userRecord);

        /**
         * If user was added via ajax, we display some data.
         * Otherwise we redirect user to current page.
         */
        return $this->response()->respondWithSuccessRedirect($userRecord->getEditUrl());
    }

    /**
     * Delete record.
     *
     * @param UserRecord $userRecord
     * @return $this
     */
    public function getDeleteAction(UserRecord $userRecord)
    {
        /**
         * Delete record with one-liner.
         */
        $userRecord->delete();

        /**
         * Respond with useful data.
         */
        return $this->response()->respondWithSuccessRedirect();
    }

}
