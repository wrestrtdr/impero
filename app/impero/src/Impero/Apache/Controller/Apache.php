<?php namespace Impero\Apache\Controller;

use Impero\Apache\Entity\Sites;
use Impero\Apache\Form\Site as SiteForm;
use Impero\Apache\Record\Site as SiteRecord;
use Pckg\Maestro\Helper\Maestro;
use Pckg\Framework\Controller;

class Apache extends Controller
{

    use Maestro;

    /**
     * List all available sites.
     *
     * @param Sites $sites
     * @return mixed
     */
    public function getIndexAction(Sites $sites)
    {
        return $this->tabelize($sites, ['server_name', 'server_alias'], 'Sites');
    }

    /**
     * Show add form.
     * Form is automatically filled with session data.
     *
     * @param SiteForm   $siteForm
     * @param SiteRecord $siteRecord
     * @return mixed
     */
    public function getAddAction(SiteForm $siteForm, SiteRecord $siteRecord)
    {
        /**
         * We may have some default values.
         */
        $siteForm->populateFromRecord($siteRecord);

        /**
         * Then we simply display form.
         */
        return $this->formalize($siteForm, $siteRecord, 'Add site');
    }

    /**
     * Save form data.
     * Form is automatically filled with request data.
     *
     * @param SiteForm   $siteForm
     * @param SiteRecord $siteRecord
     * @return mixed
     */
    public function postAddAction(SiteForm $siteForm, SiteRecord $siteRecord)
    {
        /**
         * Fill record with posted data.
         */
        $siteForm->populateToRecord($siteRecord);

        /**
         * User ID may be set if admin added it.
         */
        $siteRecord->setUserIdByAuthIfNotSet();

        /**
         * Simply save record.
         */
        $siteRecord->save();

        queue()->create('apache:restart');

        /**
         * If site was added via ajax, we display some data and redirect url.
         * Otherwise we redirect user to edit form.
         */
        return $this->response()->respondWithSuccessRedirect($siteRecord->getEditUrl());
    }

    /**
     * Show edit form.
     * Form is automatically filled with session data.
     *
     * @param SiteForm   $siteForm
     * @param SiteRecord $siteRecord
     * @return mixed
     */
    public function getEditAction(SiteForm $siteForm, SiteRecord $siteRecord)
    {
        /**
         * Fill form with record data.
         */
        $siteForm->populateFromRecord($siteRecord);

        /**
         * Then we simply display form.
         */
        return $this->formalize($siteForm, $siteRecord, 'Edit site');
    }

    /**
     * Save form data.
     * Form was automatically filled with request data.
     *
     * @param SiteForm   $siteForm
     * @param SiteRecord $siteRecord
     * @return $this
     */
    public function postEditAction(SiteForm $siteForm, SiteRecord $siteRecord)
    {
        /**
         * Fill record with posted data.
         */
        $siteForm->populateToRecordAndSave($siteRecord);

        queue()->create('apache:restart');

        /**
         * If site was added via ajax, we display some data.
         * Otherwise we redirect user to current page.
         */
        return $this->response()->respondWithSuccessRedirect($siteRecord->getEditUrl());
    }

    /**
     * Delete record.
     *
     * @param SiteRecord $siteRecord
     * @return $this
     */
    public function getDeleteAction(SiteRecord $siteRecord)
    {
        /**
         * Delete record with one-liner.
         */
        $siteRecord->delete();

        /**
         * Respond with useful data.
         */
        return $this->response()->respondWithSuccessRedirect();
    }

}
