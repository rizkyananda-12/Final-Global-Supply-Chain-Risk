<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class RiskScoringEngine {
    
    public function analyzeNewsSentiment($text) {
        $cleanText = strtolower(preg_replace('/[^a-zA-Z\s]/', '', $text));
        $words = explode(' ', $cleanText);

        $positiveWords = DB::table('positive_words')->pluck('word')->toArray();
        $negativeWords = DB::table('negative_words')->pluck('word')->toArray();

        $positiveScore = 0;
        $negativeScore = 0;

        foreach ($words as $word) {
            if (in_array($word, $positiveWords)) $positiveScore++;
            if (in_array($word, $negativeWords)) $negativeScore++;
        }

        $total = $positiveScore + $negativeScore;
        $posPercent = $total > 0 ? round(($positiveScore / $total) * 100) : 0;
        $negPercent = $total > 0 ? round(($negativeScore / $total) * 100) : 0;
        $neuPercent = $total == 0 ? 100 : (100 - ($posPercent + $negPercent));

        return [
            'positive' => $posPercent,
            'neutral' => $neuPercent,
            'negative' => $negPercent,
            'risk_rating' => $negPercent 
        ];
    }

    public function calculateTotalRisk($weatherRisk, $inflationRisk, $newsRisk, $currencyRisk) {
        $totalScore = ($weatherRisk * 0.30) + ($inflationRisk * 0.20) + ($newsRisk * 0.40) + ($currencyRisk * 0.10);
        $totalScore = round($totalScore);

        if ($totalScore <= 35) {
            $status = "Low Risk";
        } elseif ($totalScore <= 65) {
            $status = "Medium Risk";
        } else {
            $status = "High Risk";
        }

        return [
            'score' => $totalScore,
            'status' => $status
        ];
    }
}