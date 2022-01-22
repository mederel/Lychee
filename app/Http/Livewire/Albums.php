<?php

namespace App\Http\Livewire;

use App\Actions\Albums\Smart;
use App\Actions\Albums\Top;
use Barryvdh\Debugbar\Facades\Debugbar;
use Livewire\Component;

class Albums extends Component
{
	public $albums;
	public $smartalbums;
	public $shared_albums;

	/** @var Top */
	private $top;

	/** @var Smart */
	private $smart;

	/**
	 * Initialize component.
	 *
	 * @param Top   $top
	 * @param Smart $smart
	 */
	public function mount(
		Top $top,
		Smart $smart
	) {
		$this->top = $top;
		$this->smart = $smart;

		// $toplevel contains Collection<Album> accessible at the root: albums shared_albums.
		$toplevel = $this->top->get();

		$this->albums = $toplevel['albums'];
		// ->map(fn ($e) => $e->toArray());
		$this->shared_albums = $toplevel['shared_albums'];
		// ->map(fn ($e) => $e->toArray());
		$this->smartalbums = $this->smart->get();
		// ->map(fn ($e) => $e->toArray());
		Debugbar::warning($this);
		// dd($this);
	}

	/**
	 * Render component.
	 */
	public function render()
	{
		return view('livewire.albums');
	}
}
