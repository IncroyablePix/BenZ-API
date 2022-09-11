<?php

namespace SmallPHP\Models;

require_once __DIR__ . "/architecture_type.php";
require_once __DIR__ . "/system_type.php";
require_once __DIR__ . "/../ransom.php";

use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\ManyToMany;

enum BuildState: string
{
    case BUILD_STATE_PENDING = "pending";
    case BUILD_STATE_BUILDING = "building";
    case BUILD_STATE_SUCCESS = "success";
    case BUILD_STATE_FAILED = "failed";
}

/**
 * @Entity
 * @MappedSuperclass
 * @Table(name="Executable")
 */
class Executable
{
    /**
     * @var int $id
     * @Id
     * @GeneratedValue(strategy="AUTO")
     * @Column(name="id", type="integer")
     */
    private int $id;

    /**
     * @var ArchitectureType $platform_type
     * @Column(name="platform_type", type="integer")
     */
    private ArchitectureType $platform_type;

    /**
     * @var SystemType $system_type
     * @Column(name="system_type", type="integer")
     */
    private SystemType $system_type;

    /**
     * @var BuildState $build_state
     * @Column(name="build_state", type="string")
     */
    private BuildState $build_state;

    /**
     * @var string $path
     * @Column(name="path", type="string")
     */
    private ?string $path;

    /**
     * @var Ransom $ransom
     * @ManyToMany(targetEntity="Ransom", mappedBy="executables")
     */
    private Ransom $ransom;

    public function __construct(Ransom $ransom, ArchitectureType $architecture_type, SystemType $system_type)
    {
        $this->ransom = $ransom;
        $this->platform_type = $architecture_type;
        $this->system_type = $system_type;
        $this->build_state = BuildState::BUILD_STATE_PENDING;
        $this->path = null;
    }

    /**
     * @return int
     */
    public function get_id(): int
    {
        return $this->id;
    }

    public function build(): void
    {
        // TODO: Implement build() method.
    }

    public function get_path(): ?string
    {
        return $this->path;
    }

    /**
     * @return ArchitectureType
     */
    public function get_platform_type(): ArchitectureType
    {
        return $this->platform_type;
    }

    /**
     * @param ArchitectureType $platform_type
     */
    public function set_platform_type(ArchitectureType $platform_type): void
    {
        $this->platform_type = $platform_type;
    }

    /**
     * @return SystemType
     */
    public function get_system_type(): SystemType
    {
        return $this->system_type;
    }

    /**
     * @param SystemType $system_type
     */
    public function set_system_type(SystemType $system_type): void
    {
        $this->system_type = $system_type;
    }

    /**
     * @return BuildState
     */
    public function get_build_state(): BuildState
    {
        return $this->build_state;
    }

    /**
     * @param BuildState $build_state
     */
    public function set_build_state(BuildState $build_state): void
    {
        $this->build_state = $build_state;
    }

    public function to_array(): array
    {
        return [
            "id" => $this->id,
            "platform_type" => $this->platform_type->to_string(),
            "system_type" => $this->system_type->to_string(),
            "build_state" => $this->build_state->value,
        ];
    }
}
