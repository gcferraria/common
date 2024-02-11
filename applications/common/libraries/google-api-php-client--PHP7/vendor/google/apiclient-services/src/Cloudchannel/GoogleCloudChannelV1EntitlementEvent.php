<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Cloudchannel;

class GoogleCloudChannelV1EntitlementEvent extends \Google\Model
{
  /**
   * @var string
   */
  public $entitlement;
  /**
   * @var string
   */
  public $eventType;

  /**
   * @param string
   */
  public function setEntitlement($entitlement)
  {
    $this->entitlement = $entitlement;
  }
  /**
   * @return string
   */
  public function getEntitlement()
  {
    return $this->entitlement;
  }
  /**
   * @param string
   */
  public function setEventType($eventType)
  {
    $this->eventType = $eventType;
  }
  /**
   * @return string
   */
  public function getEventType()
  {
    return $this->eventType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1EntitlementEvent::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1EntitlementEvent');
