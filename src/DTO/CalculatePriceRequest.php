<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CalculatePriceRequest
{
  #[Assert\NotBlank]
  #[Assert\Positive]
  public int $product;

  #[Assert\NotBlank]
  #[Assert\Regex(
    pattern: '/^(DE\d{9}|IT\d{11}|GR\d{9}|FR[A-Z]{2}\d{9})$/',
    message: 'Invalid tax number format'
  )]
  public string $taxNumber;

  #[Assert\Optional]
  #[Assert\Length(min: 2, max: 20)]
  public ?string $couponCode = null;
}
