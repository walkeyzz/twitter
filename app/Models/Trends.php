<?php

namespace App\Models;

use CodeIgniter\Model;

class Trends extends BaseModel
{
    protected $table = 'trends';
    protected $primaryKey = 'trendID';
    protected $allowedFields = [
      'trendID',
      'hashtag'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $returnType = 'object'; // Default return type for database queries

    public function getTrends()
    {
        $builder = $this->table('trends');
        $builder->select('trends.*, COUNT(tweets.tweetID) AS tweetsCount');
        $builder->join('tweets', "tweets.status LIKE CONCAT('%#', trends.hashtag, '%') OR tweets.retweetMsg LIKE CONCAT('%#', trends.hashtag, '%')", 'inner');
        $builder->groupBy('trends.hashtag');
        $builder->orderBy('tweetsCount', 'DESC');
        $builder->limit(2);

        $query = $builder->get();
        return $query->getResult(); // Returns results as an array of objects
    }

    public function getTrendByHash($hashtag)
    {
        $builder = $this->table('trends');
        $builder->like('hashtag', $hashtag, 'after'); // 'after' makes it 'LIKE {hashtag}%'
        $builder->limit(5);
        $query = $builder->get();

        return $query->getResult();
    }

    public function addTrend($hashtag)
    {
        preg_match_all("/#+([a-zA-Z0-9_]+)/i", $hashtag, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $trend) {
                // Insert each trend into the database
                $data = [
                    'hashtag' => $trend,
                    'created_at' => date('Y-m-d H:i:s') // If your database doesn't automatically set the timestamp
                ];
                $this->insert($data);
            }
        }
    }
}
