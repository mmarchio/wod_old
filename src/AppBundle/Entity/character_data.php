<?php
namespace AppBundle\Entity;

use JsonSerializable;
use AppBundle\Service\CharacterUtils;

class character_data implements JsonSerializable
{
    private $name;
    private $player;
    private $chronicle;
    private $nature;
    private $demeanor;
    private $concept;
    private $clan;
    private $generation;
    private $sire;
    private $freebies;
    private $physical;
    private $social;
    private $mental;
    private $talents;
    private $skills;
    private $knowledges;
    private $disciplines;
    private $backgrounds;
    private $virtues;
    private $path;
    private $willpower;
    private $blood_pool;

    public function __construct(array $cp = null, $ct, $traits)
    {
        $virtues = new virtues;
        $virtues->setConscience(CharacterUtils::findTraitValue(60, $ct)->getValue())
            ->setSelfControl(CharacterUtils::findTraitValue(61, $ct)->getValue())
            ->setCourage(CharacterUtils::findTraitValue(62, $ct)->getValue());

        $path = new trait_value;
        $path->setId(0)
            ->setValue(CharacterUtils::findTraitValue(64, $ct)->getValue());

        $trait_count = count($traits);

        $this->setName($cp[0]->getName())
            ->setPlayer($cp[0]->getPlayer())
            ->setChronicle($cp[0]->getChronicle())
            ->setDemeanor($cp[0]->getDemeanor())
            ->setNature($cp[0]->getNature())
            ->setConcept($cp[0]->getConcept())
            ->setClan($cp[0]->getClan())
            ->setGeneration($cp[0]->getGeneration())
            ->setSire($cp[0]->getSire())
            ->setFreebies($cp[0]->getFreebies())
            ->setPhysical($this->setTrait($traits, $trait_count, $ct, 1))
            ->setSocial($this->setTrait($traits, $trait_count, $ct, 2))
            ->setMental($this->setTrait($traits, $trait_count, $ct, 3))
            ->setTalents($this->setTrait($traits, $trait_count, $ct, 4))
            ->setSkills($this->setTrait($traits, $trait_count, $ct, 5))
            ->setKnowledges($this->setTrait($traits, $trait_count, $ct, 6))
            ->setDisciplines($this->setDynamicTrait($traits, $trait_count, $ct, 3))
            ->setBackgrounds($this->setDynamicTrait($traits, $trait_count, $ct, 4))
            ->setVirtues($virtues)
            ->setPath($path)
            ->setWillpower(CharacterUtils::findTraitValue(63, $ct)->getValue())
            ->setBloodPool(0);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): character_data 
    {
        $this->name = $name;
        return $this;
    }

    public function getPlayer(): string 
    {
        return $this->player;
    }

    public function setPlayer(string $player): character_data 
    {
        $this->player = $player;
        return $this;
    }

    public function getChronicle(): string
    {
        return $this->chronicle;
    }

    public function setChronicle(string $chronicle): character_data 
    {
        $this->chronicle = $chronicle;
        return $this;
    }

    public function getNature()
    {
        return $this->nature;
    }

    public function setNature($nature): character_data 
    {
        $this->nature = $nature;
        return $this;
    }

    public function getDemeanor()
    {
        return $this->demeanor;
    }

    public function setDemeanor($demeanor): character_data 
    {
        $this->demeanor = $demeanor;
        return $this;
    }

    public function getConcept(): string
    {
        return $this->concept;
    }

    public function setConcept(string $concept): character_data 
    {
        $this->concept = $concept;
        return $this;
    }

    public function getClan()
    {
        return $this->clan;
    }

    public function setClan($clan): character_data 
    {
        $this->clan = $clan;
        return $this;
    }

    public function getGeneration(): int 
    {
        return $this->generation;
    }

    public function setGeneration(int $generation): character_data 
    {
        $this->generation = $generation;
        return $this;
    }

    public function getSire()
    {
        return $this->sire;
    }

    public function setSire($sire): character_data 
    {
        $this->sire = $sire;
        return $this;
    }

    public function getFreebies(): int
    {
        return $this->freebies;
    }

    public function setFreebies(int $freebies): character_data 
    {
        $this->freebies = $freebies;
        return $this;
    }

    public function getPhysical(): array 
    {
        return $this->physical;
    }

    public function setPhysical(array $physical): character_data
    {
        $this->physical = $physical;
        return $this;
    }

    public function appendPhysical($physical): character_data
    {
        $this->physical[] = $physical;
        return $this;
    }

    public function getSocial(): array
    {
        return $this->social;
    }

    public function setSocial(array $social): character_data
    {
        $this->social = $social;
        return $this;
    }

    public function appendSocial($social): character_data
    {
        $this->social[] = $social;
        return $this;
    }

    public function getMental(): array
    {
        return $this->mental;
    }

    public function setMental(array $mental): character_data
    {
        $this->mental = $mental;
        return $this;
    }

    public function appendMental($mental): character_data
    {
        $this->mental[] = $mental;
        return $this;
    }

    public function getTalents(): array
    {
        return $this->talents;
    }

    public function setTalents(array $talents): character_data
    {
        $this->talents = $talents;
        return $this;
    }

    public function appendTalents($talent): character_data
    {
        $this->talents[] = $talent;
        return $this;
    }

    public function getSkills(): array
    {
        return $this->skills;
    }

    public function setSkills(array $skills): character_data
    {
        $this->skills = $skills;
        return $this;
    }

    public function appendSkills($skill): character_data
    {
        $this->skills[] = $skill;
        return $this;
    }

    public function getKnowledges(): array
    {
        return $this->knowledges;
    }

    public function setKnowledges(array $knowledges): character_data
    {
        $this->knowledges = $knowledges;
        return $this;
    }

    public function appendKnowledges($knowledge): character_data
    {
        $this->knowledges[] = $knowledge;
        return $this;
    }

    public function getDisciplines(): array
    {
        return $this->disciplines;
    }

    public function setDisciplines(array $disciplines): character_data
    {
        $this->disciplines = $disciplines;
        return $this;
    }

    public function appendDisciplines($discipline): character_data
    {
        $this->disciplines[] = $discipline;
        return $this;
    }

    public function getBackgrounds(): array
    {
        return $this->backgrounds;
    }

    public function setBackgrounds(array $backgrounds): character_data
    {
        $this->backgrounds = $backgrounds;
        return $this;
    }

    public function appendBackgrounds($background): character_data
    {
        $this->backgrounds[] = $background;
        return $this;
    }

    public function getVirtues(): virtues 
    {
        return $this->virtues;
    }

    public function setVirtues(virtues $virtues): character_data 
    {
        $this->virtues = $virtues;
        return $this;
    }

    public function getPath(): trait_value 
    {
        return $this->path;
    }

    public function setPath(trait_value $path): character_data
    {
        $this->path = $path;
        return $this;
    }

    public function getWillpower(): int
    {
        return $this->willpower;
    }

    public function setWillpower(int $willpower): character_data
    {
        $this->willpower = $willpower;
        return $this;
    }

    public function getBloodPool(): int
    {
        return $this->blood_pool;
    }

    public function setBloodPool(int $bloodPool): character_data
    {
        $this->blood_pool = $bloodPool;
        return $this;
    }

    public function jsonSerialize(): array 
    {
        $a = [];
        foreach ($this as $k => &$v) {
            $a[$k] = $v;
        }
        return $a;
    }

    private function setTrait(array $traits, int $trait_count, $ct, int $subcategory): array
    {
        $result = [];
        for ($i=0; $i<$trait_count; $i++) {
            if ($traits[$i]->getSubCategory() === $subcategory) {
                $trait = new \stdClass();
                $trait->id = $traits[$i]->getId();
                $trait->value = CharacterUtils::findTraitValue($trait->id, $ct)->getValue();
                $trait->trait = $traits[$i]->getTrait();
                $result[] = $trait;
            }
        }
        return $result;
    }

    private function setDynamicTrait(array $traits, int $trait_count, $ct, int $category): array 
    {
        $traits = [];
        for ($i=0; $i<$trait_count; $i++) {
            if ($traits[$i]->getCategory() === 3) {
                $v = CharacterUtils::findTraitValue($traits[$i]->getId(), $ct);
                if (!empty($v)) {
                    $trait = new \stdClass();
                    $trait->id = $traits[$i]->getId();
                    $trait->value = $v->getValue();
                    $trait->name = $traits[$i]->getTrait();
                    $traits[] = $trait;
                }
            }
        }
        return $traits;
    }
}