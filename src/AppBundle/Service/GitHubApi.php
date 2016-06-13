<?php

namespace AppBundle\Service;

class GitHubApi
{
    /** @var \GuzzleHttp\Client */
    private $client;

    /**
     * GitHubApi constructor.
     */
    public function __construct($guzzleClient)
    {
        $this->client = $guzzleClient;
    }

    public function getProfile($username)
    {
        $response = $this->fetchData('users/' . $username);
        $data = json_decode($response->getBody()->getContents(), true);

        return [
            'avatar_url' => $data['avatar_url'],
            'name'       => $data['name'],
            'login'      => $data['login'],
            'details'    => [
                'company'   => $data['company'],
                'location'  => $data['location'],
                'joined_on' => 'Joined on ' . (new \DateTime($data['created_at']))->format('d M Y')
            ],
            'blog' => $data['blog'],
            'social_data' => [
                'Public Repos'  => $data['public_repos'],
                'Followers'     => $data['followers'],
                'Following'     => $data['following']
            ],
        ];
    }

    public function getRepos($username)
    {
        $response = $this->fetchData('users/' . $username . '/repos');
        $data = json_decode($response->getBody()->getContents(), true);

        return [
            'repo_count' => count($data),
            'most_stars' => array_reduce(
                $data, function($c, $i) { return $i['stargazers_count'] > $c ? $i['stargazers_count'] : $c; }
            ),
            'repos' => $data
        ];
    }

    private function fetchData($endpoint)
    {
        return $this->client->request('GET', 'https://api.github.com/' . $endpoint);
    }
}