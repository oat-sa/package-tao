<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @license GPLv2
 * @package taoDevTools
 *
 */
namespace oat\taoDevTools\helper;

/**
 * Name generator to generate searchable names
 * 
 * Based on:
 *  http://www.englishbanana.com/ (public domain) 
 *  http://study4ielts.wikispaces.com/ (Creative Commons Attribution Share-Alike 3.0)
 * 
 * @author Joel Bout <joel@taotesting.com>
 *
 */
class NameGenerator
{
    private static $nouns = array(
        "adventure","amazement","anger","anxiety","apprehension","artistry","arrogance","awe","beauty","belief","bravery","brutality","calm","cactus","chaos","charity","childhood","clarity","coldness","comfort","communication","compassion","confidence","contentment",
        "courage","crime","curiosity","death","deceit","dedication","defeat","delight","democracy","despair","determination","dexterity","dictatorship","disappointment","disbelief","disquiet","disturbance","education","ego","elegance","energy","enhancement","enthusiasm","envy","evil","excitement","failure",
        "faith","faithfulness","faithlessness","fascination","favouritism","fear","forgiveness","fragility","frailty","freedom","friendship","generosity","goodness","gossip","grace","grief","happiness","hate","hatred","hearsay","helpfulness","helplessness","homelessness","honesty","honour","hope","humility",
        "humour","hurt","idea","idiosyncrasy","imagination","impression","improvement","infatuation","inflation","insanity","intelligence","jealousy","joy","justice","kindness","knowledge","laughter","law","liberty","life","loss","love","loyalty","luck","luxury","man","maturity","memory","mercy","motivation",
        "movement","music","need","omen","opinion","opportunism","opportunity","pain","patience","peace","peculiarity","perseverance","pleasure","poverty","power","pride","principle","reality","redemption","refreshment","relaxation","relief","restoration","riches","romance","rumour","sacrifice","sadness",
        "sanity","satisfaction","self-control","sensitivity","service","shock","silliness","skill","slavery","sleep","sophistication","sorrow","sparkle","speculation","speed","strength","strictness","stupidity","submission","success","surprise","sympathy","talent","thrill","tiredness","tolerance","trust",
        "uncertainty","unemployment","unreality","victory","wariness","warmth","weakness","wealth","weariness","wisdom","wit","worry"
    );
    
    private static $adjectives = array(
        "agreeable","amused","ancient","angry","annoyed","anxious","arrogant","ashamed","average","awful","bad","beautiful","better","big","bitter","black","blue","boiling","brave","breezy","brief","bright","broad","broken","bumpy","calm","charming","cheerful","chilly","clumsy","cold","colossal",
        "combative","comfortable","confused","cooing","cool","cooperative","courageous","crazy","creepy","cruel","cuddly","curly","curved","damp","dangerous","deafening","deep","defeated","defiant","delicious","delightful","depressed","determined","dirty","disgusted","disturbed","dizzy","dry","dull","dusty",
        "eager","early","elated","embarrassed","empty","encouraging","energetic","enthusiastic","envious","evil","excited","exuberant","faint","fair","faithful","fantastic","fast","fat","few","fierce","filthy","fine","flaky","flat","fluffy","foolish","frail","frantic","fresh","friendly","frightened","funny",
        "fuzzy","gentle","giant","gigantic","good","gorgeous","greasy","great","green","grieving","grubby","grumpy","handsome","happy","hard","harsh","healthy","heavy","helpful","helpless","high","hilarious","hissing","hollow","homeless","horrible","hot","huge","hungry","hurt","hushed","husky","icy","ill",
        "immense","itchy","jealous","jittery","jolly","juicy","kind","large","late","lazy","light","little","lively","lonely","long","loose","loud","lovely","low","lucky","magnificent","mammoth","many","massive","melodic","melted","mighty","miniature","moaning","modern","mute","mysterious","narrow","nasty",
        "naughty","nervous","new","nice","nosy","numerous","nutty","obedient","obnoxious","odd","old","orange","ordinary","outrageous","panicky","perfect","petite","pleasant","precious","pretty","prickly","proud","puny","purple","purring","quaint","quick","quickest","quiet","rainy","rapid","rare","raspy",
        "ratty","red","relieved","repulsive","resonant","ripe","roasted","robust","rotten","rough","round","sad","salty","scary","scattered","scrawny","screeching","selfish","shaggy","shaky","shallow","sharp","shivering","short","shrill","silent","silky","silly","skinny","slimy","slippery","slow","small",
        "smiling","smooth","soft","solid","sore","sour","spicy","splendid","spotty","square","squealing","stale","steady","steep","sticky","stingy","straight","strange","striped","strong","successful","sweet","swiftly","tall","tame","tan","tart","tasteless","tasty","tender","tender","tense","terrible",
        "testy","thirsty","thoughtful","thoughtless","thundering","tight","tiny","tired","tough","tricky","troubled","ugliest","ugly","uneven","upset","uptight","vast","victorious","vivacious","voiceless","wasteful","watery","weak","weary","wet","whispering","wicked","wide","wide-eyed","witty","wonderful",
        "wooden","worried","yellow","young","different","popular","insufferable","unfortunate"
    );
        
    private static $ordinal = array(    
        "first", "second", "third", "fourth", "fifth", "sixth", "seventh", "eighth", "ninth", "tenth","eleventh","twelfth","thirteenth","fourteenth","fifteenth","sixteenth","seventeenth","eighteenth","nineteenth","twentieth"
    );
    
    protected static function getRandomNoun($excluded = null) {
        do {
            $cand = self::$nouns[rand(0, count(self::$nouns) - 1)];
        } while (!is_null($excluded) && $cand === $excluded);
        
        return $cand; 
    }
    
    protected static function getRandomAdj() {
        return self::$adjectives[rand(0, count(self::$adjectives) - 1)];
    }
    
    protected static function addAn($word) {
        return in_array(substr($word, 0, 1), array('a', 'i', 'u', 'e', 'o', 'y'))
            ? 'an '.$word
            : 'a '.$word;
    }
    
    /**
     * Generates a random title for a resource 
     * 
     * @return string
     */
    public static function generateTitle() {
        $first = self::getRandomNoun();
        $second = self::getRandomNoun($first);
        
        switch (rand(0, 6)) {
        	case 0:
                return 'The '.$first.' of '.self::getRandomAdj().' '.$second;
        	case 1:
        	    return ucfirst($first).' and '.ucfirst($second);
        	case 2:
        	    return 'The '.self::getRandomAdj().' '.$second;
    	    case 3:
    	        return ucfirst(self::addAn(self::getRandomAdj())).' '.$second;
        	case 4:
        	    return 'The '.$first.' in '.$second;
    	    case 5:
    	        return 'Of '.$first.' and '.$second;
    	    case 6:
    	        return ucfirst(self::getRandomAdj()).' '.ucfirst($second);
        	         
        }
    }

    /**
     * Generates a string composed of random characters
     * 
     * @param number $length
     * @return string
     */
    public static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
}
