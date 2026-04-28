<?php
// classes/OpenLibraryConnector.php

class OpenLibraryConnector {
    private $baseUrl = 'https://openlibrary.org/search.json';

    /**
     * Search Open Library
     * @param string $query Search term
     * @param string $type Search type (q, title, author, isbn)
     * @param int $limit Number of results
     * @return array List of books
     */
    public function search($query, $type = 'q', $limit = 12) {
        $qParam = '';
        
        // Construct query based on type
        if ($type === 'title') {
            $qParam = 'title:' . $query;
        } elseif ($type === 'author') {
            $qParam = 'author:' . $query;
        } elseif ($type === 'isbn') {
            $qParam = 'isbn:' . $query;
        } else {
            $qParam = $query;
        }

        $url = $this->baseUrl . '?q=' . urlencode($qParam) . '&limit=' . $limit;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local dev
        // It is polite to include a User-Agent for Open Library
        curl_setopt($ch, CURLOPT_USERAGENT, "LibraryApp/1.0 (Simple PHP Client)");

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            error_log('OpenLibrary API Error: ' . curl_error($ch));
            curl_close($ch);
            return [];
        }
        curl_close($ch);

        if ($httpCode !== 200) {
            error_log('OpenLibrary API HTTP Error: ' . $httpCode);
            return [];
        }

        return $this->parseResponse($response);
    }

    private function parseResponse($jsonString) {
        $data = json_decode($jsonString, true);
        if (!isset($data['docs'])) return [];

        $books = [];
        foreach ($data['docs'] as $doc) {
            $coverUrl = null;
            if (isset($doc['cover_i'])) {
                $coverUrl = "https://covers.openlibrary.org/b/id/" . $doc['cover_i'] . "-L.jpg";
            }

            // Map Language
            $lang = 'id'; // Default
            if (isset($doc['language'])) {
                if (in_array('ind', $doc['language'])) $lang = 'id';
                elseif (in_array('eng', $doc['language'])) $lang = 'en';
                elseif (in_array('jpn', $doc['language'])) $lang = 'jp';
                elseif (in_array('ara', $doc['language'])) $lang = 'ar';
            }

            $books[] = [
                'title' => $doc['title'] ?? 'Unknown Title',
                'author' => isset($doc['author_name']) ? implode(', ', array_slice($doc['author_name'], 0, 3)) : 'Unknown Author',
                'year' => $doc['first_publish_year'] ?? '-',
                'publisher' => isset($doc['publisher']) ? implode(', ', array_slice($doc['publisher'], 0, 1)) : '-',
                'publish_location' => isset($doc['publish_place']) ? implode(', ', array_slice($doc['publish_place'], 0, 1)) : '',
                'isbn' => isset($doc['isbn']) ? $doc['isbn'][0] : null,
                'pages' => $doc['number_of_pages_median'] ?? null,
                'language' => $lang,
                'subjects' => isset($doc['subject']) ? implode(', ', array_slice($doc['subject'], 0, 5)) : '',
                'ddc' => isset($doc['ddc']) ? $doc['ddc'][0] : '',
                'cover' => $coverUrl,
                'key' => $doc['key'] ?? null,
                'link' => 'https://openlibrary.org' . ($doc['key'] ?? '')
            ];
        }
        return $books;
    }
}
?>