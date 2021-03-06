<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\user\widgets;

use Yii;
use yii\bootstrap\Html;

/**
 * UserFollowButton
 *
 * @author luke
 * @since 0.11
 */
class UserFollowButton extends \yii\base\Widget
{

    /**
     * @var \humhub\modules\user\models\User
     */
    public $user;

    /**
     * @var string label for follow button (optional)
     */
    public $followLabel = null;

    /**
     * @var string label for unfollow button (optional)
     */
    public $unfollowLabel = null;

    /**
     * @var string options for follow button 
     */
    public $followOptions = ['class' => 'btn btn-primary'];

    /**
     * @var array options for unfollow button 
     */
    public $unfollowOptions = ['class' => 'btn btn-info'];

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->followLabel === null) {
            $this->followLabel = Yii::t("UserModule.widgets_views_followButton", "Follow");
        }
        if ($this->unfollowLabel === null) {
            $this->unfollowLabel = Yii::t("UserModule.widgets_views_followButton", "Unfollow");
        }

        if (!isset($this->followOptions['class'])) {
            $this->followOptions['class'] = "";
        }
        if (!isset($this->unfollowOptions['class'])) {
            $this->unfollowOptions['class'] = "";
        }

        if (!isset($this->followOptions['style'])) {
            $this->followOptions['style'] = "";
        }
        if (!isset($this->unfollowOptions['style'])) {
            $this->unfollowOptions['style'] = "";
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->user->isCurrentUser() || \Yii::$app->user->isGuest) {
            return;
        }

        // Add class for javascript handling
        $this->followOptions['class'] .= ' followButton';
        $this->unfollowOptions['class'] .= ' unfollowButton';

        // Hide inactive button
        if ($this->user->isFollowedByUser()) {
            $this->followOptions['style'] .= ' display:none;';
        } else {
            $this->unfollowOptions['style'] .= ' display:none;';
        }

        // Add UserId Buttons
        $this->followOptions['data-userid'] = $this->user->id;
        $this->unfollowOptions['data-userid'] = $this->user->id;

        #$this->view->registerJsFile('@web/resources/user/followButton.js');
        $this->view->registerJs(<<<JS
$(document).on('click', '.unfollowButton', function (event) {
    var userId = $(this).data("userid");
    $.ajax({
        url: $(this).attr("href"),
        type: "POST",
        success: function () {
            $(".unfollowButton[data-userid='" + userId + "']").hide();
            $(".followButton[data-userid='" + userId + "']").show();
        }
    });
    event.preventDefault();
});

$(document).on('click', '.followButton', function (event) {
    var userId = $(this).data("userid");
    $.ajax({
        url: $(this).attr("href"),
        type: "POST",
        success: function () {
            $(".unfollowButton[data-userid='" + userId + "']").show();
            $(".followButton[data-userid='" + userId + "']").hide();
        }
    });
    event.preventDefault();
});
JS
);

        return Html::a($this->unfollowLabel, $this->user->createUrl('/user/profile/unfollow'), $this->unfollowOptions) .
                Html::a($this->followLabel, $this->user->createUrl('/user/profile/follow'), $this->followOptions);
    }

}
