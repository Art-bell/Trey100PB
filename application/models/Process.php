<?php 
class process extends CI_Model {
	function __construct()
	{
		parent::__construct();
	}

	function newRepeatedBarsEntry($artist, $album, $song, $repeated_bar_count)
	{
		$sql_query = 'SELECT id FROM repeated_bars WHERE artist = ? AND song = ?';
		$cleaned = $this->db->query($sql_query, array($artist, $song));
		if ($cleaned->num_rows() == 0)
		{
			$sql_query = 'INSERT INTO repeated_bars (artist, album, song, bar_repetitions) VALUES (?, ?, ?, ?)';
			$cleaned = $this->db->query($sql_query, array($artist, $album, $song,$repeated_bar_count));
		}
		return;

	}

	function newEntry($artist, $album, $song, $data)
	{
		foreach ($data as $key => $value)
		{
			//Check if word is in
			$sql_query = 'SELECT id FROM song_and_album_data WHERE artist = ? AND album = ? AND song = ? AND word = ?';
			$cleaned = $this->db->query($sql_query, array($artist, $album, $song, $key));
			if ($cleaned->num_rows() == 0)
			{
				$sql_query = 'INSERT INTO song_and_album_data (artist, album, song, word, frequency) VALUES (?, ?, ?, ?, ?)';
				$this->db->query($sql_query, array($artist, $album, $song, $key, $value));
			}
		}
		return;

	}

	function plotAlbum($artist, $album)
	{
		$sql_query = 'SELECT SUM(frequency) as totalCount, word from trey100.song_and_album_data WHERE artist = ? AND album = ? GROUP BY word  ORDER BY totalCount DESC';
		$cleaned = $this->db->query($sql_query, array($artist, $album));
		return $cleaned;
	}

	function addBarCount($artist, $album, $bars)
	{
		//Check if bar exist
			$sql_query = 'SELECT id FROM bar_data WHERE artist = ? AND album = ?';
			$cleaned = $this->db->query($sql_query, array($artist, $album));
			if ($cleaned->num_rows() == 0)
			{
				$sql_query = 'INSERT INTO bar_data (artist, album, total_bars) VALUES (?, ?, ?)';
				$this->db->query($sql_query, array($artist, $album, $bars));
			}
			// else {
			// 	$sql_query = 'UPDATE bar_data SET total_bars=total_bars+? WHERE artist = ? AND album = ?';
			// 	$this->db->query($sql_query, array($bars, $artist, $album));
			// 	return;
			// }
	}
}

?>