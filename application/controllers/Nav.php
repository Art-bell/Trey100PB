<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Nav extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	function __construct()
	{
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
	}
	public function index()
	{
		$_SESSION['check'] = $_SESSION['check'] + 1;

		echo "CHECKER ".$_SESSION['check']."\n"; 	
	}

	public function viewSongData()
	{
		$this->load->view('viewSongData');  	
	}

	public function viewAlbumData()
	{
		$this->load->view('viewAlbumData');  	
	}

	public function song_data()
	{
		$artist = "lil uzi vert";
		$album = "luv is rage";
		$song = "paradise";
		$myfile = fopen(__DIR__ . "/../../assets/Songs/lil_uzi_vert/luv_is_rage/Track16_Paradise.txt", "r") or die("Unable to open file!");

		//Remove special characters

		$data = fread($myfile,filesize(__DIR__ . "/../../assets/Songs/lil_uzi_vert/luv_is_rage/Track16_Paradise.txt"));
		$patterns = array();
    	$patterns[0] = "/[^A-Za-z0-9 ]/";
    	$newList = array();
    	$replacements = array();
		$replacements[0] = "";
		$data = preg_split("/[\s,\n]+/", $data);

		foreach ($data as $value){
			$temp = preg_replace($patterns, $replacements, $value);
			$temp = strtolower($temp);
			if (array_key_exists($temp,$newList))
			{
				$newList[$temp] += 1;
			}
			else {
				$newList[$temp] = 1;
			}

		}   
		$this->load->model('process');
		$this->process->newEntry($artist, $album, $song, $newList);
		arsort($newList);
		echo json_encode($newList);

	}

	// public function totalAdlibCount() {

	// }

	// public function barsEndingWithAdlibs() {

	// }

	public function repeatedOrSimilarBars()
	{
		$this->load->model('process');
		$artist = "lil uzi vert";
		$album = "luv is rage";
		$dir = new DirectoryIterator(__DIR__ . "/../../assets/Songs/lil_uzi_vert/luv_is_rage/Lyrics/");
		$total_repetitions = 0;
		foreach ($dir as $fileinfo) {
			$repeated_bar_count = 0;
		    if (!$fileinfo->isDot() && substr($fileinfo->getFilename(),-4) == ".txt") {
			// echo substr($fileinfo->getFilename(), 0,-4)."<br>";

		        $myfile = fopen(__DIR__ . "/../../assets/Songs/lil_uzi_vert/luv_is_rage/Lyrics/".$fileinfo->getFilename(), "r") or die("Unable to open file!");
		        $data = fread($myfile , filesize(__DIR__ . "/../../assets/Songs/lil_uzi_vert/luv_is_rage/Lyrics/".$fileinfo->getFilename()));
				$data = explode("\n", $data);
				$parent = array();
				foreach ($data as $bar) {
					array_push($parent, explode(" ", $bar));
					// echo "<br>";
				}
				$map = array();
				$available = array();
				$non_chorus = 1;
				//Create index map
				foreach ($parent as $line_numA => $lyric_lineA) {
					$currator = array();
					foreach ($lyric_lineA as $wordA) {
						$currator[$wordA] = 0;
					}
					array_push($available, 1);
					$map[$line_numA] = $currator;

				}

				foreach ($parent as $line_numA => $lyric_lineA) {
					//Check if any other line has matched with this
					if ($data[$line_numA] == "*REPEATED CHORUS" || $data[$line_numA] == "*FEATURE")
					{
						$non_chorus = 0;
					}
					if ($available[$line_numA] == 1 && $non_chorus)
					{
						
						foreach ($lyric_lineA as $wordA) {
							foreach ($map as $line_numB => $wordArray) {
								if ($line_numA != $line_numB)
								{
									//Check for match
									if (isset($wordArray[$wordA]))
									{
										$wordArray[$wordA] += 1;
										$map[$line_numB] = $wordArray;
									}
								}
							}

						}
						//sum each array and compare sum to size or if >= 4
						// print_r($lyric_lineA);
						// echo "<br>";
						$lyric_size = count($lyric_lineA);
						foreach ($map as $line_numB => $inner_array) {
							// $loops += 1;
							$sum = array_sum($inner_array);
							// print_r($inner_array);

							// echo "\t\t\tSum " . $sum;
							// echo "<br>";

							//End once one repetition is found
							//Match with percentage of line
							if ($available[$line_numB] && ($sum >= (int)(0.8*$lyric_size) || $sum == count($inner_array)))
							{
								$repeated_bar_count += 1;
								$total_repetitions += 1;
								$available[$line_numB] = 0;
								$available[$line_numA] = 0;
								break;
							}
						}
						//Reset map
						foreach ($map as $line_numB => $wordArray) {
							foreach ($wordArray as $key => $word) {
								$wordArray[$key] = 0;
							}
							$map[$line_numB] = $wordArray;
						}
						// echo "<br><br><br>";
						// break;
					}
					if ($data[$line_numA] == "#REPEATED CHORUS" || $data[$line_numA] == "#FEATURE")
					{
						$non_chorus = 1;
					}
				}

				// print_r($map);
			echo "Reapeated bars for " .substr($fileinfo->getFilename(), 0,-4). ": ".$repeated_bar_count."<br>";	
			$this->process->newRepeatedBarsEntry($artist, $album, substr($fileinfo->getFilename(), 0,-4), $repeated_bar_count);

		    }
		}
		    echo "TOTAL: ".$total_repetitions."<br>";


	}

	public function plotAlbum()
	{
		$artist = "lil uzi vert";
		$album = "luv is rage";

		$this->load->model('process');
		$dataPlot = $this->process->plotAlbum($artist, $album);
		$keyValPairs = array();
		foreach ($dataPlot->result() as $value) {
			$keyValPairs[$value->word] = intval($value->totalCount);
		}
		//print_r($keyValPairs);
		arsort($keyValPairs);
		echo json_encode($keyValPairs);
	}

	public function executeBarCount()
	{
		$artist = 'lil uzi vert';
		$album = 'luv is rage';
		$bars = 0;
		$dir = new DirectoryIterator(__DIR__ . "/../../assets/Songs/lil_uzi_vert/luv_is_rage/Lyrics/");
		foreach ($dir as $fileinfo) {
		    if (!$fileinfo->isDot() && substr($fileinfo->getFilename(),-4) == ".txt") {
		        $myfile = fopen(__DIR__ . "/../../assets/Songs/lil_uzi_vert/luv_is_rage/Lyrics/".$fileinfo->getFilename(), "r") or die("Unable to open file!");
		        $data = fread($myfile , filesize(__DIR__ . "/../../assets/Songs/lil_uzi_vert/luv_is_rage/Lyrics/".$fileinfo->getFilename()));
				$data = explode("\n", $data);
				foreach ($data as $bar) {
					$bars += 1;
				}
				
		    }
		}
		$this->load->model('process');
		$this->process->addBarCount($artist, $album, $bars);
		


		// //Remove special characters

		
	}

	public function totalBars()
	{
		$this->load->view('totalBars');
		//print_r($_SERVER);
	}

	public function categorizeBars()
	{
		$proceed = 0;
		$total_bars = 0;
		$myfile = fopen(__DIR__ . "/../../assets/Songs/lil_uzi_vert/luv_is_rage/Luv_Is_Rage_Notes.txt", "r") or die("Unable to open file!");
		$data = fread($myfile , filesize(__DIR__ . "/../../assets/Songs/lil_uzi_vert/luv_is_rage/Luv_Is_Rage_Notes.txt"));
		$data = explode("\n", $data);
		foreach ($data as $line) {
			$I_count = 0;
			if ($proceed)
			{
				// echo $line . "\n";

				$key_value = explode(":", $line);
				print_r($key_value);
				$I_count += strlen($key_value[1]);
				$total_bars += $I_count;
				echo $I_count."<br>";

			}
			if (!$proceed && substr($line, -3) == "***")
			{
				$proceed = 1;
			}
		}
		echo "TOTAL: ". $total_bars;
	}

}
