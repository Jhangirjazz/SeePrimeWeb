<?php

namespace App\Livewire;

use Livewire\Component;

class SeasonSelector extends Component
{
    public $seasons = [];
    public $selectedSeason = '1';
    public $contentId;
    public $episodesBySeason = [];
    public $filteredEpisodes = [];

    public function mount($seasons, $selectedSeason, $contentId, $episodesBySeason)
    {
        $this->seasons = $seasons;
        $this->selectedSeason = (string) $selectedSeason;
        $this->contentId = $contentId;
        $this->episodesBySeason = $episodesBySeason;
        $this->updateFilteredEpisodes();
    }

    public function updatedSelectedSeason($value)
    {
        $this->selectedSeason = (string) $value;
        $this->filteredEpisodes = $this->episodesBySeason[$this->selectedSeason] ?? [];
    }
    public function updateFilteredEpisodes()
    {
        $this->filteredEpisodes = $this->episodesBySeason[$this->selectedSeason] ?? [];
    }
    public function render()
    {
        return view('livewire.season-selector',[
            'filteredEpisodes' => $this->filteredEpisodes, 
        ]);
    }
}
