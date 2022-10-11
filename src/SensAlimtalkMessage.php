<?php


namespace Comento\SensAlimtalk;

use Illuminate\Support\Carbon;

class SensAlimtalkMessage
{
    public $plusFriendId;
    public $templateCode;
    public $to;
    public $content;
    public $buttons;
    public $failoverConfigContent;
    public $reserveTime;
    public $countryCode;
    public $variables;
    public $utmSource;
    public $custom_pattern = '/#{\w.+}/';

    /**
     * SensAlimtalkMessage constructor.
     */
    public function __construct()
    {
        $this->plusFriendId = config('sens-alimtalk.plus_friend_id');
        $this->messages = [];
    }

    /**
     * @param $templateCode
     * @return $this
     */
    public function templateCode($templateCode): SensAlimtalkMessage
    {
        $this->templateCode = $templateCode;

        return $this;
    }

    /**
     * @param $to
     * @return $this
     */
    public function to($to): SensAlimtalkMessage
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @param $content
     * @return $this
     */
    public function content($content): SensAlimtalkMessage
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @param $linkMobile
     * @return $this
     */
    public function linkMobile($linkMobile): SensAlimtalkMessage
    {
        $this->linkMobile = $linkMobile;

        return $this;
    }

    /**
     * @param $button
     * @return $this
     */
    public function button($button): SensAlimtalkMessage
    {
        $this->buttons[] = $button;

        return $this;
    }

    /**
     * @param $linkPc
     * @return $this
     */
    public function linkPc($linkPc): SensAlimtalkMessage
    {
        $this->linkPc = $linkPc;

        return $this;
    }

    /**
     * reservation Time
     * format: yyyy-MM-dd HH:mm
     *
     * @param $reserveTime
     * @return $this
     */
    public function reserveTime($reserveTime): SensAlimtalkMessage
    {
        $this->reserveTime = $reserveTime;

        return $this;
    }

    /**
     * @param string $countryCode
     * @return $this
     */
    public function countryCode(string $countryCode = '+82'): SensAlimtalkMessage
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * @param $minutes
     * @return $this
     * @throws \Exception
     */
    public function reserveAfterMinute($minutes): SensAlimtalkMessage
    {
        if ($minutes <= 10) {
            throw new \Exception('SensAlimtalkMessage error: Reservation cannot be requested within 10 minutes.');
        } else if ($minutes > 60 * 24 * 180) {
            throw new \Exception('SensAlimtalkMessage error: Reservations can be made in up to 180 days.');
        }

        $this->reserveTime = Carbon::now()->addMinutes($minutes)->isoFormat('YYYY-MM-DD HH:mm');

        return $this;
    }

    /**
     * @param $days
     * @return $this
     * @throws \Exception
     */
    public function reserveAfterDay($days): SensAlimtalkMessage
    {
        if ($days > 180) {
            throw new \Exception('SensAlimtalkMessage error: Reservations can be made in up to 180 days.');
        }

        $this->reserveTime = Carbon::now()->addDays($days)->isoFormat('YYYY-MM-DD HH:mm');

        return $this;
    }

    /**
     * @param $variables
     * @return $this
     */
    public function variables($variables): SensAlimtalkMessage
    {
        $this->variables = $variables;

        return $this;
    }

    /**
     * @param $utmSource
     * @return $this
     */
    public function utmSource($utmSource): SensAlimtalkMessage
    {
        $this->utmSource = $utmSource;

        return $this;
    }

    /**
     * @param $check_text
     * @return void
     */
    public function setVariables(&$check_text)
    {
        foreach ($this->variables as $key => $value) {
            if (stripos($check_text, '#{' . $key . '}') !== false) {
                $check_text = str_replace('#{' . $key . '}', $value, $check_text);
            }
        }
    }

    /**
     * @param $selectPlusFriendId
     * @return $this
     */
    public function setPlusFriendId($selectPlusFriendId): SensAlimtalkMessage
    {
        $this->plusFriendId = $selectPlusFriendId;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        if (!is_array($this->to)) {
            $this->to = [$this->to];
        }

        // Change variables
        if (preg_match($this->custom_pattern, $this->content, $matches) > 0) {
            $this->setVariables($this->content);
        }

        // If there is utm_source, attach it to the link.
        if (isset($this->buttons)) {
            foreach ($this->buttons as &$button) {
                if (isset($button['linkMobile']) && !empty($this->utmSource)) {
                    $button['linkMobile'] .= (stripos($button['linkMobile'], '?') !== false) ?
                        '&' . $this->utmSource :
                        '?' . $this->utmSource;
                }
                if (isset($button['linkPc']) && !empty($this->utmSource)) {
                    $button['linkPc'] .= (stripos($button['linkPc'], '?') !== false) ?
                        '&' . $this->utmSource :
                        '?' . $this->utmSource;
                }
            }
        }

        foreach ($this->to as $t) {
            $this->messages[] = [
                "to" => $t,
                "content" => $this->content,
                "buttons" => $this->buttons,
                "countryCode" => $this->countryCode,
                "failoverConfig" => [
                    "content" => $this->failoverConfigContent ??
                        $this->content . "\n\n" . ($this->buttons[0]['linkMobile'] ?? '')
                ]
            ];
        }

        return [
            "plusFriendId" => $this->plusFriendId,
            "templateCode" => $this->templateCode,
            "messages" => $this->messages,
            "reserveTime" => $this->reserveTime,
        ];
    }
}
