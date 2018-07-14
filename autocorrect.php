<?php

function wordExist ($word = null) {
	// check if there is one parameter
	if ($word === null) {
		echo "The function wordExist expects one parameter.\n";

		return false;
	}
	// check if the parameter is a string
	if (!is_string($word)) {
		echo "The function wordExist expects one parameter to be string.\n";

		return false;
	}
	// open the dictionnary
	$dictionnary = fopen('english_dictionnary.txt', 'r');

	// clean the word
	$word = trim($word);

	// read line by line the dictionnary
	while (!feof($dictionnary)) {
		$line = fgets($dictionnary);

		// clean the lines
		$line = str_replace("\n", '', $line);
		$line = str_replace("\r", '', $line);

		// check if the word exists in the dictionnary
		if ($word === $line) {

			return true;
		}
	}
	// close the dictionnary
	fclose($dictionnary);

	return false;
}

function getNearestLetters ($letter = null) {
	// check if there is one parameter
	if ($letter === null) {
		echo "The function getNearestLetters expects one parameter.\n";

		return false;
	}
	// check if the parameter is a string
	if (!is_string($letter)) {
		echo "The function getNearestLetters expects one parameter to be string.\n";

		return false;
	}

	// clean the letter
	$letter = trim($letter);

	// check if there is only one character
	if (strlen($letter) > 1) {
		echo "The function getNearestLetters expects one parameter to be a single letter.\n";

		return false;
	}
	// creating keyboard separated by line
	$keyboard = "azertyuiop|qsdfghjklm|wxcvbn";
	$result = [];

	// Find the position of the first occurrence of the letter (case-insensitive) in the keyboard
	$position = stripos($keyboard, $letter);

	if ($position !== false) {
		// check if there is a letter at the left
		if ($position - 1 >= 0 && $keyboard[$position - 1] && $keyboard[$position - 1] != '|') {
			array_push($result, $keyboard[$position - 1]);
		}
		// check if there is a letter at the right
		if ($position + 1 < strlen($keyboard) && $keyboard[$position + 1] && $keyboard[$position + 1] != '|') {
			array_push($result, $keyboard[$position + 1]);
		}
	}
	
	return $result;
}

function autocorrect ($word = null) {
	// init the start time to calculate executing time of the script
	$starttime = time();

	// check if there is one parameter
	if ($word === null) {
		echo "The function wordExist expects one parameter.\n";

		return false;
	}
	// check if the parameter is a string
	if (!is_string($word)) {
		echo "The function wordExist expects one parameter to be string.\n";

		return false;
	}
	// clean the word
	$word = trim($word);

	$result = [];
	$other_words = [];

	// if the word exists, push it in the result array
	if (wordExist($word)) {
		array_push($result, $word);
	}

	// split the word into an array
	foreach (str_split($word) as $key => $letter) {
		// get the nearest letters for each letter
		$n_letters = getNearestLetters($letter);
		foreach ($n_letters as $key => $n_letter) {
			// check if the word exists with one letter replaced
			$new_word = str_replace($letter, $n_letter, $word);
			if (wordExist($new_word)) {
				// push the word into the result array if it exists
				if (!in_array($new_word, $result)) {
					array_push($result, $new_word);
				}
			} else {
				// push into a new array if it doesn't exist
				if (!in_array($new_word, $other_words)) {
					array_push($other_words, $new_word);
				}
			}
		}
	}

	// do the same algorithm with the new words that didn't exist while there is no word found
	while (empty($result)) {
		// stop the script after 10 seconds without results
		$now = time() - $starttime;
		if ($now > 10) {
			echo "Sorry we didn't find the word !\n";
			break;
		}
		foreach ($other_words as $key => $word) {
			foreach (str_split($word) as $key => $letter) {
				$n_letters = getNearestLetters($letter);
				foreach ($n_letters as $key => $n_letter) {
					$new_word = str_replace($letter, $n_letter, $word);
					if (wordExist($new_word)) {
						if (!in_array($new_word, $result)) {
							array_push($result, $new_word);
						}
					} else {
						if (!in_array($new_word, $other_words)) {
							array_push($other_words, $new_word);
							// remove the word that didn't exist from the array $other_words to speed up the algorithm
							if (($k = array_search($word, $other_words)) !== false) {
								unset($other_words[$k]);
							}
						}
					}
				}
			}
		}
	}
	echo "Execution time : " . (time() - $starttime) . "s\n";
	return $result;
}



// Some examples to test the script

// var_dump(wordExist('sun'));
// var_dump(getNearestLetters('q'));
// var_dump(autocorrect('hrllp'));

