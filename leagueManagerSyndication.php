﻿<?php
/**
 * ************************
 * Do not change this file!
 * ************************
 */

/**
 * CSS classes used in the HTML generated by this class file
 *    div.leagman
 *    table.leagman_league
 *    tr.leagman_league_head
 *    tr.leagman_division_head
 *    tr.leagman_promotion
 *    tr.leagman_relegation
 *    th/td.leagman_column_pos
 *    th/td.leagman_column_team_name
 *    th/td.leagman_column_played
 *    th/td.leagman_column_won
 *    th/td.leagman_column_drawn
 *    th/td.leagman_column_lost
 *    th/td.leagman_column_goals_f
 *    th/td.leagman_column_goals_a
 *    th/td.leagman_column_goal_diff
 *    th/td.leagman_column_points
 *    th/td.leagman_column_ma_points
 */
class LeagueManagerTables {
   var $leagues;
   var $url;

   /**
    * Constructor
    * @param $league Defines the leagues and divisions you want to be displayed here
    *                The key to each array element is the numeric ID of the league to be read from the LeagueManager site
    *                and each array element value is an array of numeric division IDs to be displayed, or an empty array
    *                to display all divisions for the league
    */
   function LeagueManagerTables($leagues) {
      $this->leagues = $leagues;
      $this->url = "http://yorkshireha.org.uk/e107_plugins/league_manager/data/tables_";
      //$this->url = "../league_manager/data/tables_";
   }

   /**
    * Get the HTML for the requested league tables.
    * @return        the HTML for the requested league tables.
    */
   function getHTML() {
      $showthisdivision = false;
      $text = "";
      $text .= "<div class='leagman'>\n";
      foreach ($this->leagues as $leagueid=>$divisions) {
         $text .= $this->showLeague($leagueid, $divisions);
      }
      $text .= "</div>\n";
      return $text;
   }

   /**
    * @private
    */
   function showLeague($leagueid, $divisions) {
      $text = "<p>There was a problem retrieving data for league ID $leagueid.</p>";
      if (file_exists($this->url.$leagueid.".json")) {
         $data = json_decode(file_get_contents($this->url.$leagueid.".json"), true);
         $text = "<table class='leagman_league'>\n";
         $text .= $this->getLeagueHead($data["league"]);
         foreach($data["league"]["divisions"] as $division) {
            if (count($divisions) === 0 || array_search($division["id"], $divisions) !== false) {
               $text .= $this->getDivisionHead($division);
               foreach($division["teams"] as $team) {
                  $text .= $this->getDivisionRow($team);
               }
               $text .= $this->getDivisionFoot($division);
            }
         }
         $text .= "</table>\n";
         $text .= $this->getLeagueFoot($data["league"]);
      }
      return $text;
   }

   /**
    * Get the HTML for the start of a league table
    * Can be overridden if needed. Note that when called this fuction is normally epecting to output HTML for a table row
    * @param $league Associative array of league details
    */
   function getLeagueHead($league) {
      $text = "";
      $text .= "<tr class='leagman_league_head'>";
      //$text .= "<td colspan='11'>\n".$league["name"]." ".$league["season"]."</td>";
      $text .= "<td colspan='11'>\n".$league["name"]."</td>";
      $text .= "</tr>\n";
      return $text;
   }

   /**
    * Get the HTML for the end of a league table
    * Can be overridden if needed. Note that when called this fuction is normally epecting to output HTML for a table row
    * @param $league Associative array of league details
    */
   function getLeagueFoot($league) {
      $text = "";
      return $text;
   }

   /**
    * Get the HTML for the start of a division
    * Can be overridden if needed. Note that when called this fuction is normally epecting to output HTML for a table row
    * @param $division Associative array of division details
    */
   function getDivisionHead($division) {
      $text = "";
      $text .= "<tr class='leagman_division_head'>";
      $text .= "<td colspan='11'>".$division["longName"]."</td>";
      $text .= "</tr>\n";
      $text .= "<tr>";
      $text .= "<th class='leagman_column_pos'>Pos</th>";
      $text .= "<th class='leagman_column_team_name'>Team</th>";
      $text .= "<th class='leagman_column_played'>Ply</th>";
      $text .= "<th class='leagman_column_won'>W</th>";
      $text .= "<th class='leagman_column_drawn'>D</th>";
      $text .= "<th class='leagman_column_lost'>L</th>";
      $text .= "<th class='leagman_column_goals_f'>GF</th>";
      $text .= "<th class='leagman_column_goals_a'>GA</th>";
      $text .= "<th class='leagman_column_goal_diff'>+/-</th>";
      $text .= "<th class='leagman_column_points'>Pts</th>";
      $text .= "<th class='leagman_column_ma_points'>Max Pts</th>";
      $text .= "</tr>\n";
      return $text;
   }

   /**
    * Get the HTML for a row in a division
    * Can be overridden if needed. Note that when called this fuction is normally epecting to output HTML for a table row
    * @param $division Associative array of division details
    */
   function getDivisionRow($team) {
      $extraclass = "";
      $image = "";
      if (isset($team["promotion"]) && $team["promotion"]!="0") {
         $extraclass .= $team["promotion"]=="1" ? " leagman_promotion" : " leagman_relegation";
      }
      $text = "";
      $text .= "<tr class='leagman_division_row{$extraclass}'>";
      $text .= "<td class='leagman_column_pos'>".$team["pos"]."</td>";
      $text .= "<td class='leagman_column_team_name'>".$team["teamName"]."</td>";
      $text .= "<td class='leagman_column_played'>".$team["played"]."</td>";
      $text .= "<td class='leagman_column_won'>".$team["won"]."</td>";
      $text .= "<td class='leagman_column_drawn'>".$team["drawn"]."</td>";
      $text .= "<td class='leagman_column_lost'>".$team["lost"]."</td>";
      $text .= "<td class='leagman_column_goals_f'>".$team["goalsF"]."</td>";
      $text .= "<td class='leagman_column_goals_a'>".$team["goalsA"]."</td>";
      $text .= "<td class='leagman_column_goal_diff'>".($team["goalsF"]-$team["goalsA"])."</td>";
      $text .= "<td class='leagman_column_points'>".$team["points"]."</td>";
      $text .= "<td class='leagman_column_ma_points'>".$team["maxPoints"]."</td>";
      $text .= "</tr>\n";
      return $text;
   }

   /**
    * Get the HTML for the end of a division
    * Can be overridden if needed. Note that when called this fuction is normally epecting to output HTML for a table row
    * @param $division Associative array of division details
    */
   function getDivisionFoot($division) {
      $text = "";
      return $text;
   }
}

/**
 * CSS classes used in the HTML generated by this class file
 *    div.leagman
 *    table.leagman_fixtures
 *    table.leagman_fixtures td.division
 */
class LeagueManagerFixtures {
   var $url;

   /**
    * Constructor
    * @param $clubId Defines the club ID you want to be displayed
    */
   function LeagueManagerFixtures($clubId) {
      $this->clubId = $clubId;
      $this->url = "http://localhost/yorkshireha.org.uk/e107_plugins/league_manager/data/fixtures_".$clubId.".json";
      //$this->url = "../league_manager/data/fixtures_".$clubId.".json";
   }

   /**
    * Get the HTML for the requested club.
    * @param   $options an options array, allowed options are:
    *                   - includeScores (boolean) include scores in output (true) or not (false, default)
    *                   - startDate (timestamp) show fixtures from this date, default is today
    *                   - daysAhead (integer) show fixtures from start date plus this number of days, defaults to all fixtures from start date
    * @return           the HTML for the requested club.
    */
   function getHTML($options=null) {
      $text = "<p>There was a problem retrieving data for club ID $this->clubId.</p>";
      if (file_exists($this->url)) {
         $data = json_decode(file_get_contents($this->url), true);
         $curDate = "";
         $text = "<div class='leagman'>\n";
         $text .= "<table class='leagman_fixtures'>\n";
         $days = isset($options['daysAhead']) ? 60*60*24*$options['daysAhead'] : 60*60*24*9999;
         $dateFormat = isset($options['dateFormat']) ? $options['dateFormat'] : "d-m-Y";
         $zebra = false;
         $startDate = isset($options['startDate']) ? $options['startDate'] : mktime(0,0,0,7,1,date('Y'));
         $startDate -= 3600;
         foreach($data['fixtures'] as $fixture) {
         	$processThisFixture = $fixture['date'] > $startDate && $fixture['date'] < $startDate+($days);
         	$processThisFixture = $processThisFixture && (!isset($options['league']) || $fixture['league'] === $options['league']);
         	$processThisFixture = $processThisFixture && (!isset($options['teams']) || (in_array($fixture['homeTeam'], $options['teams']) || in_array($fixture['awayTeam'], $options['teams'])));
            if ($processThisFixture) {
               if ($fixture['date'] != $curDate) {
                  $text .= "<tr class='date'><td class='forumheader3' colspan='8'>".date($dateFormat, $fixture['date'])."</td></tr>";
                  $curDate = $fixture['date'];
               }
               $text .= "<tr class='".($zebra ? "even" : "odd")."'>";
               $text .= "<td class='leagman_league'>".$fixture['league']."</td>";
               $text .= "<td class='leagman_division'>".$fixture['divisionLong']."</td>";
               $text .= "<td class='leagman_time'>".$fixture['timeText']."</td>";
               if (isset($options['stackTeams']) && $options['stackTeams']===true) {
                  $text .= "<td class='leagman_homeAwayTeam'>".$fixture['homeTeam']."<br/>v ".$fixture['awayTeam']."</td>";
                  if (isset($options['includeScores']) && $options['includeScores']===true) {
                     $text .= "<td class='leagman_homeAwayScore'>".$fixture['homeScore']."<br/>".$fixture['awayScore']."</td>";
                  }
               } else {
                  $text .= "<td class='leagman_homeTeam'>".$fixture['homeTeam']."</td>";
                  if (isset($options['includeScores']) && $options['includeScores']===true) {
                     $text .= "<td class='leagman_homeScore'>".$fixture['homeScore']."</td>";
                     $text .= "<td class='leagman_awayScore'>".$fixture['awayScore']."</td>";
                  }
                  $text .= "<td class='leagman_awayTeam'>".$fixture['awayTeam']."</td>";
               }
               $text .= "</tr>";
               $zebra = !$zebra;
            }
         }
         $text .= "</table>\n";
         $text .= "</div>\n";
      }
      return $text;
   }
}
?>