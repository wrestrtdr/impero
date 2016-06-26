<?php namespace Gnp\Platform\Controller;

use Gnp\Platform\Entity\Platforms;
use Gnp\Platform\Record\Platform as PlatformRecord;
use Pckg\Framework\Controller;

class Platform extends Controller
{

    public function getUserHeaderAction(Platforms $platforms) {
        return view(
            'Gnp\Platform:userHeader',
            [
                'platforms'       => $platforms->all(),
                'currentPlatform' => $platforms->where('id', $_SESSION['platform_id'])->one(),
            ]
        );
    }

    public function getSwitchPlatformAction(PlatformRecord $platform) {
        $_SESSION['platform_id'] = $platform->id;

        return $this->response()->respondWithSuccessRedirect();
    }

}