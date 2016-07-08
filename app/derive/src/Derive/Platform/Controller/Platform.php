<?php namespace Derive\Platform\Controller;

use Derive\Platform\Entity\Platforms;
use Derive\Platform\Record\Platform as PlatformRecord;
use Pckg\Framework\Controller;

class Platform extends Controller
{

    public function getUserHeaderAction(Platforms $platforms) {
        return view(
            'Derive\Platform:userHeader',
            [
                'platforms'       => $platforms->forUser($this->auth()->getUser())->all(),
                'currentPlatform' => $platforms->where('id', $_SESSION['platform_id'])->one(),
            ]
        );
    }

    public function getSwitchPlatformAction(PlatformRecord $platform) {
        $_SESSION['platform_id'] = $platform->id;

        return $this->response()->respondWithSuccessRedirect();
    }

}