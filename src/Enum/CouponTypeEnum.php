<?php

namespace App\Enum;

enum CouponTypeEnum: string
{
  case Fixed = 'fixed';
  case Percent = 'percent';
}
