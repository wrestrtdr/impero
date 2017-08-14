<?php namespace Impero\Mysql\Controller;

use Impero\Mysql\Entity\Databases;
use Impero\Mysql\Form\Database as DatabaseForm;
use Impero\Mysql\Record\Database as DatabaseRecord;
use Pckg\Framework\Controller;
use Pckg\Maestro\Helper\Maestro;

class Database extends Controller
{

    use Maestro;

    /**
     * List all available databases.
     *
     * @param Databases $databases
     *
     * @return mixed
     */
    public function getIndexAction(Databases $databases)
    {
        return $this->tabelize($databases, ['name'], 'Databases');
    }

    /**
     * Show add form.
     * Form is automatically filled with session data.
     *
     * @param DatabaseForm   $databaseForm
     * @param DatabaseRecord $databaseRecord
     *
     * @return mixed
     */
    public function getAddAction(DatabaseForm $databaseForm, DatabaseRecord $databaseRecord)
    {
        /**
         * We may have some default values.
         */
        $databaseForm->populateFromRecord($databaseRecord);

        /**
         * Then we simply display form.
         */
        return $this->formalize($databaseForm, $databaseRecord, 'Add database');
    }

    /**
     * Save form data.
     * Form is automatically filled with request data.
     *
     * @param DatabaseForm   $databaseForm
     * @param DatabaseRecord $databaseRecord
     *
     * @return mixed
     */
    public function postAddAction(DatabaseForm $databaseForm, DatabaseRecord $databaseRecord)
    {
        /**
         * Fill record with posted data.
         */
        $databaseForm->populateToRecord($databaseRecord);

        /**
         * User ID may be set if admin added it.
         */
        $databaseRecord->setUserIdByAuthIfNotSet();

        /**
         * Simply save record.
         */
        $databaseRecord->save();

        /**
         * If database was added via ajax, we display some data and redirect url.
         * Otherwise we redirect user to edit form.
         */
        return $this->response()->respondWithSuccessRedirect($databaseRecord->getEditUrl());
    }

    /**
     * Show edit form.
     * Form is automatically filled with session data.
     *
     * @param DatabaseForm   $databaseForm
     * @param DatabaseRecord $databaseRecord
     *
     * @return mixed
     */
    public function getEditAction(DatabaseForm $databaseForm, DatabaseRecord $databaseRecord)
    {
        /**
         * Fill form with record data.
         */
        $databaseForm->populateFromRecord($databaseRecord);

        /**
         * Then we simply display form.
         */
        return $this->formalize($databaseForm, $databaseRecord, 'Edit database');
    }

    /**
     * Save form data.
     * Form was automatically filled with request data.
     *
     * @param DatabaseForm   $databaseForm
     * @param DatabaseRecord $databaseRecord
     */
    public function postEditAction(DatabaseForm $databaseForm, DatabaseRecord $databaseRecord)
    {
        /**
         * Fill record with posted data.
         */
        $databaseForm->populateToRecordAndSave($databaseRecord);

        /**
         * If database was added via ajax, we display some data.
         * Otherwise we redirect user to current page.
         */
        return $this->response()->respondWithSuccessRedirect($databaseRecord->getEditUrl());
    }

    /**
     * Delete record.
     *
     * @param DatabaseRecord $databaseRecord
     */
    public function getDeleteAction(DatabaseRecord $databaseRecord)
    {
        /**
         * Delete record with one-liner.
         */
        $databaseRecord->delete();

        /**
         * Respond with useful data.
         */
        return $this->response()->respondWithSuccessRedirect();
    }

}
