<?php namespace Derive\Offers\Controller;

use Derive\Offers\Entity\Offers as OffersEntity;
use Pckg\Framework\Controller;

class Offers extends Controller
{

    public function getHomepageAction(OffersEntity $offers)
    {
        return view(
            'Derive\Offers:offers\homepage',
            [
                'offers' => $offers->forHomepage()->all(),
            ]
        );
    }

    public function getListAction(OffersEntity $offers)
    {
        $offersArr = $offers->forListing()->all();

        return view(
            'Derive\Offers:offers\list',
            [
                'offers' => $offersArr,
            ]
        );

        $i = 0;

        //default to # of big and medium offers
        $big = 2;
        $med = 4;
        $medMax = 8;

        $cOffers = $offersArr->total();

        //medium sized offers must be 2 in line
        if ($cOffers <= $big + $medMax) {
            $med = $medMax;

            if (($cOffers - $big) % 2 != 0) {
                $big += 1;
            }
        }

        while ($rOffer = $this->db->f($q)) {
            $offerId = $rOffer['id'];

            $rOffer["pickupLine"] = $rOffer["pickup"];

            //place is always printed, date only if available
            $rOffer["date"] = (is_null($rOffer['dt_start'])) ? "" : $this->dateFormat(
                $rOffer['dt_start'],
                $rOffer['dt_end']
            );
            $rOffer["location"] = $rOffer['ctitle'] . ", " . $rOffer['cotitle'];

            //gets minimum price for the party
            $qMinPrice = $this->db->q(
                "SELECT MIN(p.price) AS price
																	FROM packets p
																	WHERE offer_id = $offerId
																		AND dt_published <= NOW()
																		AND dt_published > '" . DT_NULL . "'"
            );
            $rMinPrice = $this->db->f($qMinPrice);

            //saves the price and if it's not null it shows the price tag
            $rOffer["price"] = (int)$rMinPrice['price'];

            $rOffer["image"] = IMAGES . $rOffer["picture"];
            $rOffer["image_cache"] = '/app/' . APP . '/cache/img/w/570/' . $rOffer["picture"];

            $rOffer["url"] = Router::make(
                "offer",
                [
                    "url" => SEO::niceUrl($rOffer['title']),
                    "id"  => $rOffer['id'],
                ]
            );

            //define this offer size
            if ($i < $big) {
                $rOffer['size'] = 'big';
            } else if ($i < $big + $med) {
                $rOffer['size'] = 'medium';
            } else {
                $rOffer['size'] = 'small';
            }

            $rOffer['break'] = ($rOffer['size'] == 'medium' && (count(
                                                                    $arrOffers
                                                                ) - $big) % 2 == 1) || ($rOffer['size'] != 'medium') ? true : false;

            //first two offers are big, others are medium
            $rOffer['n'] = $i;

            //make sure to insert big and medium offers
            $inserted = false;

            if ($rOffer['size'] == 'small') {
                $g = 0;
                //go through list to insert current offer
                foreach ($arrOffers as $index => $offer) {
                    $g++;

                    //we only check small offers
                    if ($offer['size'] != 'small') {
                        continue;
                    }

                    //this offer's category is later than current one
                    if ($rOffer['caposition'] > $offer['caposition']) {
                        continue;
                    } //this offer's category is the same one
                    else if ($rOffer['caposition'] == $offer['caposition']) {
                        //this offer's position is later than the current one
                        if ($rOffer['position'] > $offer['position']) {
                            $rOffer['catitle'] = "";
                            continue;
                        } //this is the spot
                        else {
                            //push offer to this spot
                            $first_part = array_slice($arrOffers, 0, $g - 1);
                            $second_part = array_slice($arrOffers, $g - 1);
                            $arrOffers = array_merge($first_part, [$rOffer], $second_part);
                            $inserted = true;

                            //remove category title from previous one
                            $arrOffers[$g]['catitle'] = "";

                            break;
                        }
                    } //this offer's category before the current one
                    else {
                        $first_part = array_slice($arrOffers, 0, $g - 1);
                        $second_part = array_slice($arrOffers, $g - 1);
                        $arrOffers = array_merge($first_part, [$rOffer], $second_part);
                        $inserted = true;
                        break;
                    }
                }
            }

            if (!$inserted) {
                $arrOffers[] = $rOffer;
            }

            $i++;
        }

        //first small offer is different
        if ($cOffers > $big + $medMax) {
            $arrOffers[$big + $med]['first'] = true;
        }

        //SEO::title(__("seo_title_homepage"));
        //SEO::description(__("seo_description_homepage"));

        return view(
            'Derive\Offers:offers\list',
            [
                'offers' => $offersArr,
            ]
        );
    }

}