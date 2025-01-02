<?php

namespace App\ShopifyApp\Objects\Values;

use App\ShopifyApp\Contracts\Objects\Values\ThemeId as ThemeIdValue;
use App\ShopifyApp\Contracts\Objects\Values\ThemeName as ThemeNameValue;
use App\ShopifyApp\Contracts\Objects\Values\ThemeRole as ThemeRoleValue;
use Funeralzone\ValueObjects\CompositeTrait;
use Funeralzone\ValueObjects\ValueObject;
use Illuminate\Support\Arr;

/**
 * Used to inject current session data into the user's model.
 * TODO: Possibly move this to a composite VO?
 */
final class MainTheme implements ValueObject
{
    use CompositeTrait;

    /**
     * Theme id
     *
     * @var ThemeId
     */
    protected $id;

    /**
     * Theme name
     *
     * @var ThemeName
     */
    protected $name;

    /**
     * Theme role
     *
     * @var ThemeRole
     */
    protected $role;

    /**
     * __construct
     *
     * @param ThemeIdValue $id
     * @param ThemeNameValue $name
     * @param ThemeRoleValue $role
     */
    public function __construct(ThemeIdValue $id, ThemeNameValue $name, ThemeRoleValue $role)
    {
        $this->id = $id;
        $this->name = $name;
        $this->role = $role;
    }

    /**
     * {@inheritDoc}
     */
    public static function fromNative($native)
    {
        return new static(
            NullableThemeId::fromNative(Arr::get($native, 'id')),
            NullableThemeName::fromNative(Arr::get($native, 'name')),
            NullableThemeRole::fromNative(Arr::get($native, 'role'))
        );
    }

    /**
     * Get theme id
     *
     * @return  ThemeId
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get theme name
     *
     * @return  ThemeName
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get theme role
     *
     * @return  ThemeRole
     */
    public function getRole()
    {
        return $this->role;
    }
}
