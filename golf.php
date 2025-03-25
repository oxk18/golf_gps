<?php
include_once('./_common.php');
if (!defined('_GNUBOARD_')) exit; // 그누보드 관련 필수 파일 포함

/*
// Add password protection with 24-hour session expiration
session_start();
$correct_password = "";

// Check if session exists and hasn't expired
if (isset($_SESSION['authenticated']) && isset($_SESSION['auth_time'])) {
    $current_time = time();
    $session_age = $current_time - $_SESSION['auth_time'];
    
    // If session is older than 24 hours (86400 seconds), destroy it
    if ($session_age > 86400) {
        session_unset();
        session_destroy();
        unset($_SESSION['authenticated']);
        unset($_SESSION['auth_time']);
    }
}

if (!isset($_SESSION['authenticated'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
        if ($_POST['password'] === $correct_password) {
            $_SESSION['authenticated'] = true;
            $_SESSION['auth_time'] = time(); // Store authentication time
        } else {
            echo '<div style="text-align: center; margin: 20px;">잘못된 비밀번호입니다.</div>';
        }
    }
    
    if (!isset($_SESSION['authenticated'])) {
        echo '
        <div style="text-align: center; margin: 20px; padding: 15px;">
            <form method="POST" style="max-width: 300px; margin: 0 auto;">
                <div style="margin-bottom: 15px;">
                    <input type="password" 
                           name="password" 
                           placeholder="비밀번호를 입력하세요" 
                           required 
                           style="width: 100%;
                                  padding: 12px;
                                  border: 1px solid #ccc;
                                  border-radius: 4px;
                                  font-size: 16px;
                                  box-sizing: border-box;">
                </div>
                <input type="submit" 
                       value="입력" 
                       style="width: 100%;
                              padding: 12px;
                              background: #4CAF50;
                              color: white;
                              border: none;
                              border-radius: 4px;
                              font-size: 16px;
                              cursor: pointer;">
            </form>
        </div>';
        exit;
    }
}
// Add password protection with 24-hour session expiration
*/

// Just keep 24-hour session without asking password
if (isset($_SESSION['golf_session']) && isset($_SESSION['session_time'])) {
    $current_time = time();
    $session_age = $current_time - $_SESSION['session_time'];
    
    // If session is older than 24 hours (86400 seconds), destroy it
    if ($session_age > 86400) {
        session_unset();
        session_destroy();
        unset($_SESSION['golf_session']);
        unset($_SESSION['session_time']);
    }
}

// Create new session if it doesn't exist
if (!isset($_SESSION['golf_session'])) {
    $_SESSION['golf_session'] = true;
    $_SESSION['session_time'] = time();
}



$g5['title'] = '골프 거리 측정 & 스코어보드';
include_once(G5_PATH.'/head.php');
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
</head>
<!-- 거리 측정기 컨테이너 -->
<div id="golf-distance-container">
        
        <div class="mobile-notice">자기 현재위치 공유를 허락하면 자동으로 현재위치를 보여줍니다.</div>
        <div class="mobile-notice">또는 아래 사항들을 선택해주십시오.</div> 
         
        <div id="player-options" style="margin-bottom: 15px;">
            <select id="player-gender" style="margin-right: 10px; padding: 8px;">
                <option value="M">남성</option>
                <option value="F">여성</option>
            </select>
            <select id="player-level" style="padding: 8px;">
                <option value="beginner">초보자</option>
                <option value="amateur">아마추어</option>
                <option value="pro">프로</option>
            </select>
        </div>      
        
    <div id="search-box" style="margin-bottom: 10px;">
        <input id="address-input" type="text" placeholder="주소를 입력하세요 such as 5342 Aldeburgh Drive Suwanee, GA USA" style="width: 100%; padding: 8px;">
        <button onclick="searchLocation()">검색</button>       
    </div> 
    
    <div id="map"></div>
    

    <!-- Add Scoring System -->
<div id="scoring-system" style="margin-top: 20px; padding: 15px; background: #f5f5f5; border-radius: 5px;">
    <h3 style="margin-bottom: 15px; font-size: 16px;">골프 스코어 기록</h3>
    
    <!-- Player Setup -->
    <div id="player-setup" style="margin-bottom: 20px;">
    <button onclick="addPlayer()" id="add-player-btn" style="padding: 8px 15px; background: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px;">플레이어 추가</button>
    <button onclick="startGame()" id="start-game-btn" style="padding: 8px 15px; background: #2196F3; color: white; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px;">게임 시작</button>
    <button onclick="resetGame()" style="padding: 8px 15px; background: #f44336; color: white; border: none; border-radius: 4px; cursor: pointer;">초기화</button>
</div>

    <!-- Score Table -->
    <div id="score-table-container" style="overflow-x: auto;">
        <table id="score-table" style="width: 100%; border-collapse: collapse; display: none;">
            <thead>
                <tr>
                    <th style="padding: 10px; border: 1px solid #ddd; background: #4CAF50; color: white;">플레이어</th>
                </tr>
            </thead>
            <tbody id="score-tbody">
            </tbody>
            <!--
            <tfoot>
                <tr id="total-row">
                    <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">총점</td>
                </tr>
            </tfoot>
            -->
        </table>
    </div>

    <!-- Rankings -->
    <div id="rankings" style="margin-top: 20px; display: none;">
        <h3 style="margin-bottom: 15px; font-size: 16px;">순위</h4>
        <ul id="rankings-list" style="list-style: none; padding: 0;"></ul>
    </div>
</div>

<!-- Add Distance Converter -->
<div id="distance-converter">
    <h4>거리 변환기</h4>
    <div class="converter-inputs">
        <div>
            <input type="number" id="yards" placeholder="야드" oninput="convertDistance('yards')">
            <label>야드</label>
        </div>
        <div style="display: flex; align-items: center; justify-content: center; padding: 0 10px;">
            <span style="font-size: 20px; color: #666;">⇄</span>
        </div>
        <div>
            <input type="number" id="meters" placeholder="미터" oninput="convertDistance('meters')">
            <label>미터</label>
        </div>
    </div>
</div>
    <!-- Add Distance Converter -->
<!-- Add Swing Analysis Section -->
<div id="swing-analysis-container" style="margin-bottom: 20px;">
        <h3 style="margin-bottom: 15px; font-size: 16px;">골프 스윙 분석 (스윙하는 동영상을 올려주세요)</h3>
        <form id="upload-form" method="POST" enctype="multipart/form-data" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; max-width: 800px;">
    <input type="file" id="swing-video" name="swing-video" accept="video/*" style="margin-bottom: 10px; width: 100%;">
    <div style="display: flex; gap: 10px; width: 100%;">
        <button type="submit" style="padding: 8px 15px; background: #4CAF50; color: white; border: none; border-radius: 4px; flex: 1;">업로드</button>
        <button type="button" onclick="closeVideo()" style="padding: 8px 15px; background: #f44336; color: white; border: none; border-radius: 4px; flex: 1;">닫기</button>
    </div>
        </form>

        <!-- Add reference link -->
         <br>
         <div style="margin-bottom: 15px;">
    <p>참고 영상:</p>
    <div style="position: relative; width: 100%; max-width: 1200px; margin: 10px 0;">
        <div style="display: flex; gap: 20px;">
            <div style="flex: 1; position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">
                <iframe 
                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" 
                    src="https://www.youtube.com/embed/Jlp8G9paliw" 
                    title="Tiger Woods Slow Mo Driver Swing" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen>
                </iframe>
            </div>
            <div style="flex: 1; position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">
                <iframe 
                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" 
                    src="https://www.youtube.com/embed/v8lh6Ct-32U" 
                    title="Dustin Johnson Slow Mo Iron Swing" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen>
                </iframe>
            </div>
        </div>
    </div>
</div>

<div id="video-container" style="margin-top: 20px; display: none; position: relative; max-width: 400px; margin-left: auto; margin-right: auto;">
    <video id="swing-video-player" style="width: 100%; height: auto; max-height: 300px; object-fit: contain; margin-bottom: 10px; position: relative; z-index: 1;">
        Your browser does not support the video tag.
    </video>
    <canvas id="analysis-canvas" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: auto; z-index: 2;"></canvas>
    
    <div id="analysis-controls" style="position: relative; z-index: 19; margin-top: 10px; display: flex; flex-wrap: wrap; gap: 5px;">
        <button onclick="playVideo()" style="flex: 1; min-width: 80px; padding: 8px;">재생</button>
        <button onclick="pauseVideo()" style="flex: 1; min-width: 80px; padding: 8px;">중지</button>
        <button onclick="prevFrame()" style="flex: 1; min-width: 80px; padding: 8px;">이전 프레임</button>
        <button onclick="nextFrame()" style="flex: 1; min-width: 80px; padding: 8px;">다음 프레임</button>
        <button onclick="toggleDrawing()" style="flex: 1; min-width: 80px; padding: 8px;">선 그리기</button>
        <button onclick="clearCanvas()" style="flex: 1; min-width: 80px; padding: 8px;">지우기</button>
    </div>
</div>
</div>
<!-- Add Swing Analysis Section -->        

</div>

<script>
function convertDistance(from) {
    if (from === 'yards') {
        const yards = document.getElementById('yards').value;
        const meters = (yards * 0.9144).toFixed(1);
        document.getElementById('meters').value = meters;
    } else {
        const meters = document.getElementById('meters').value;
        const yards = (meters * 1.0936).toFixed(1);
        document.getElementById('yards').value = yards;
    }
}    
let map;
let markers = [];
let polyline;
let totalDistance = 0;
let isFirstMarker = true;
let infoWindow = null;

// Add these new functions for the scoring system
let players = [];
let currentHole = 1;
const MAX_PLAYERS = 8;
const MAX_HOLES = 18;

function addPlayer() {
    if (players.length >= MAX_PLAYERS) {
        alert('최대 8명까지만 추가할 수 있습니다.');
        return;
    }

    const playerName = prompt('플레이어 이름을 입력하세요:');
    if (playerName) {
        players.push({
            name: playerName,
            scores: new Array(MAX_HOLES).fill(0)
        });
        saveGameState();
        updateScoreTable();

        // Add confirmation message
        alert(`${playerName}님이 추가되었습니다. (${players.length}/8)`);
        
        // Update start game button text
        const startBtn = document.getElementById('start-game-btn');
        if (players.length > 0) {
            startBtn.textContent = `게임 시작 (${players.length}명)`;
            startBtn.style.background = '#2196F3';
        }
    }
}
function resetGame() {
    const confirmReset = confirm('정말로 모든 스코어를 초기화하시겠습니까? 이 작업은 되돌릴 수 없습니다.');
    if (confirmReset) {
        players = [];
        localStorage.removeItem('golfGameState');
        document.getElementById('score-table').style.display = 'none';
        document.getElementById('rankings').style.display = 'none';
        document.getElementById('add-player-btn').disabled = false;
        document.getElementById('add-player-btn').style.opacity = '1';
        document.getElementById('start-game-btn').textContent = '게임 시작';

        // Clear rankings list
        const rankingsList = document.getElementById('rankings-list');
        rankingsList.innerHTML = '';

        updateScoreTable();
    }
}


function startGame() {
    if (players.length === 0) {
        alert('최소 1명의 플레이어가 필요합니다.');
        return;
    }
    
    const confirmStart = confirm(`${players.length}명의 플레이어로 게임을 시작하시겠습니까?`);
    if (confirmStart) {
        // Clear previous rankings
        const rankingsList = document.getElementById('rankings-list');
        rankingsList.innerHTML = '';

        // Disable player addition and start button
        document.getElementById('add-player-btn').disabled = true;
        document.getElementById('add-player-btn').style.opacity = '0.5';
        document.getElementById('start-game-btn').disabled = true;
        document.getElementById('start-game-btn').style.opacity = '0.5';
        document.getElementById('score-table').style.display = 'table';
        document.getElementById('rankings').style.display = 'block';
        updateScoreTable();
        alert('게임이 시작되었습니다. 즐거운 라운딩 되세요!');
    }


    document.getElementById('score-table').style.display = 'table';
    document.getElementById('rankings').style.display = 'block';
    updateScoreTable();
}

function updateScoreTable() {
    const table = document.getElementById('score-table');
    const thead = table.querySelector('thead tr');
    const tbody = document.getElementById('score-tbody');
    const totalRow = document.getElementById('total-row');

    // Clear existing headers except player column
    while (thead.children.length > 1) {
        thead.removeChild(thead.lastChild);
    }
    
    // Add total score column header
    const totalHeader = document.createElement('th');
    totalHeader.textContent = '총점';
    totalHeader.style.padding = '10px';
    totalHeader.style.border = '1px solid #ddd';
    totalHeader.style.background = '#4CAF50';
    totalHeader.style.color = 'white';
    thead.appendChild(totalHeader);


    // Add hole headers
    for (let i = 1; i <= MAX_HOLES; i++) {
        const th = document.createElement('th');
        th.textContent = `${i}번홀`;
        th.style.padding = '10px';
        th.style.border = '1px solid #ddd';
        th.style.background = '#4CAF50';
        th.style.color = 'white';
        thead.appendChild(th);
    }

    // Clear existing rows
    tbody.innerHTML = '';

    // Add player rows
    players.forEach((player, playerIndex) => {
        const row = document.createElement('tr');
        // Add name cell
        const nameCell = document.createElement('td');
        nameCell.textContent = player.name;
        nameCell.style.padding = '10px';
        nameCell.style.border = '1px solid #ddd';
        row.appendChild(nameCell);

        // Add total score cell
        const totalCell = document.createElement('td');
        const totalScore = player.scores.reduce((sum, score) => sum + score, 0);
        totalCell.textContent = totalScore;
        totalCell.style.padding = '10px';
        totalCell.style.border = '1px solid #ddd';
        totalCell.style.fontWeight = 'bold';
        totalCell.style.background = '#f5f5f5';
        row.appendChild(totalCell);

        // Add score cells
        player.scores.forEach((score, holeIndex) => {
            const cell = document.createElement('td');
            cell.style.padding = '10px';
            cell.style.border = '1px solid #ddd';
            cell.innerHTML = `<input type="number" min="1" value="${score || ''}" 
                style="width: 50px; padding: 5px;" 
                onchange="updateScore(${playerIndex}, ${holeIndex}, this.value)">`;
            row.appendChild(cell);
        });

        tbody.appendChild(row);
    });

    //updateTotalsAndRankings();
}

function updateScore(playerIndex, holeIndex, score) {
    players[playerIndex].scores[holeIndex] = parseInt(score) || 0;
    saveGameState();
    // Update the total score cell for this player
    const playerRow = document.getElementById('score-tbody').children[playerIndex];
    const totalScore = players[playerIndex].scores.reduce((sum, score) => sum + score, 0);
    playerRow.children[1].textContent = totalScore;
    
    // Update rankings
    updateRankings();
}

function updateRankings() {
    const rankingsList = document.getElementById('rankings-list');
    rankingsList.innerHTML = '';

    const rankings = [...players]
        .map(player => ({
            name: player.name,
            total: player.scores.reduce((sum, score) => sum + score, 0)
        }))
        .sort((a, b) => a.total - b.total);

        rankings.forEach((player, index) => {
        const li = document.createElement('li');
        li.textContent = `${index + 1}위: ${player.name} (${player.total}타)`;
        li.style.padding = '10px';
        li.style.margin = '5px 0';
        li.style.borderRadius = '4px';
        
        // Set background colors for top 3 positions
        if (index === 0) {
            li.style.backgroundColor = '#FFD700'; // Gold
            li.style.color = '#000';
        } else if (index === 1) {
            li.style.backgroundColor = '#C0C0C0'; // Silver
            li.style.color = '#000';
        } else if (index === 2) {
            li.style.backgroundColor = '#CD7F32'; // Bronze
            li.style.color = '#000';
        } else {
            li.style.backgroundColor = 'white';
        }
        
        rankingsList.appendChild(li);
    });
}

function updateTotalsAndRankings() {
    // Update totals
    const totalRow = document.getElementById('total-row');
    totalRow.innerHTML = '<td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">총점</td>';
    
    players.forEach(player => {
        const total = player.scores.reduce((sum, score) => sum + score, 0);
        const cell = document.createElement('td');
        cell.textContent = total;
        cell.style.padding = '10px';
        cell.style.border = '1px solid #ddd';
        cell.style.fontWeight = 'bold';
        totalRow.appendChild(cell);
    });

    // Update rankings
    const rankingsList = document.getElementById('rankings-list');
    rankingsList.innerHTML = '';

    const rankings = [...players]
        .map(player => ({
            name: player.name,
            total: player.scores.reduce((sum, score) => sum + score, 0)
        }))
        .sort((a, b) => a.total - b.total);

    rankings.forEach((player, index) => {
        const li = document.createElement('li');
        li.textContent = `${index + 1}위: ${player.name} (${player.total}타)`;
        li.style.padding = '5px 0';
        rankingsList.appendChild(li);
    });
}

function saveGameState() {
    const gameState = {
        players: players,
        timestamp: Date.now()
    };
    localStorage.setItem('golfGameState', JSON.stringify(gameState));
}

function loadGameState() {
    const savedState = localStorage.getItem('golfGameState');
    if (savedState) {
        const gameState = JSON.parse(savedState);
        const age = Date.now() - gameState.timestamp;
        
        // Check if the saved state is less than 24 hours old
        if (age < 24 * 60 * 60 * 1000) {
            players = gameState.players;
            updateScoreTable();
            document.getElementById('score-table').style.display = 'table';
            document.getElementById('rankings').style.display = 'block';
        } else {
            localStorage.removeItem('golfGameState');
        }
    }
}

// Load saved game state when the page loads
window.addEventListener('load', loadGameState);


function populateGolfCourses() {
    const select = document.createElement('select');
    select.id = 'golf-course-select';
    select.style.width = '100%';
    select.style.padding = '8px';
    select.style.marginBottom = '10px';
    
    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.text = '조지아 골프장 선택';
    select.appendChild(defaultOption);

    // Sort the array alphabetically by name before creating options
    const sortedCourses = [...georgiaGolfCourses].sort((a, b) => 
        a.name.localeCompare(b.name)
    );
    
    sortedCourses.forEach(course => {
        const option = document.createElement('option');
        option.value = course.address;
        option.text = course.name;
        select.appendChild(option);
    });
    
    select.addEventListener('change', function() {
    if (this.value) {
        document.getElementById('address-input').value = this.value;
        const geocoder = new google.maps.Geocoder();
        
        geocoder.geocode({ address: this.value }, function(results, status) {
            if (status === 'OK') {
                map.setCenter(results[0].geometry.location);
                map.setZoom(17);  // 골프장이 잘 보이는 줌 레벨로 설정
                map.setMapTypeId('satellite');  // 위성 지도로 변경
                
                // 기존 마커와 선 초기화
                clearMarkers();
                
                // 클릭 이벤트 리스너 재설정
                google.maps.event.clearListeners(map, 'click');
                map.addListener('click', function(e) {
                    if (markers.length < 2) {
                        placeMarker(e.latLng);
                    }
                });
            } else {
                alert('골프장 위치를 찾을 수 없습니다: ' + status);
            }
        });
    }
});
    
    // Insert the select element before the search box
    const searchBox = document.getElementById('search-box');
    searchBox.parentNode.insertBefore(select, searchBox);
}

// Add this array at the top of your script section
const georgiaGolfCourses = [
    { name: "Sky Valley Country Club", address: "568 Sky Valley Way, Sky Valley, GA 30537" },
    { name: "Fairfield Plantation Golf & Country Club", address: "7500 Monticello Dr, Villa Rica, GA 30180" },
    { name: "Cateechee", address: "140 Cateechee Trl, Hartwell, Georgia 30643" }, 
    { name: "Steel Canyon Golf Club", address: "460 Morgan Falls Rd, Sandy Springs, GA 30350" },
    { name: "Applewood Golf Course", address: "6130 Story Mill Rd, Keysville, GA 30816" },
    { name: "The King and Prince Beach & Golf Resort", address: "100 TabbyStone, Saint Simons Island, GA 31522" },
    { name: "The Club at Osprey Cove", address: "123 Osprey Circle, Saint Marys, GA 31558" },
    { name: "Arrowhead Pointe At Lake Richard B. Russell", address: "2790 Olympic Rowing Drive, Elberton, GA 30635" },
    { name: "Chimney Oaks Golf Club", address: "148 Hammers Glen Dr, Homer, GA 30547" },
    { name: "Sanctuary Golf Club", address: "2050 Sanctuary Wynd, Waverly, Georgia 31565" },
    { name: "University of Georgia Golf Course", address: "2600 Riverbend Road Athens, GA 30605" },
    { name: "Brasstown Valley Resort", address: "6321 US Hwy 76, Young Harris, GA 30582" },
    { name: "Little Ocmulgee At Wallace Adams Course", address: "Hwy 441 S, McRae, Georgia 31055" },
    { name: "East at Bull Creek Golf Course", address: "7333 Lynch Rd, Midland, GA 31820" },
    { name: "Sapelo Hammock Golf Club", address: "1354 Marshview Drive NE, Shellman Bluff, GA 31331" },
    { name: "Woodmont Golf & Country Club", address: "3105 Gaddis Road, Canton, GA 30115" },
    { name: "Lakes Golf Course", address: "5500 Laura Walker Rd, Waycross, GA 31503" },
    { name: "Sea Palms Golf & Tennis Resort", address: "515 North Windward Drive, St Simons Island, GA 31522" },
    { name: "Meadow Links Golf Course", address: "Bagby State Park, Fort Gaines, GA 31751" },
    { name: "Stonebridge Golf Club", address: "585 Stonebridge Dr NW, Rome, GA 30165" },
    { name: "Heritage Oaks Golf Club", address: "126 Clipper Bay, Brunswick, GA 31523" },
    { name: "Crossroads Golf Club", address: "232 James B Blackburn Dr, Savannah, GA 31408" },
    { name: "Currahee Golf Course", address: "1 Currahee Club Way, Toccoa, GA 30577" },
    { name: "Achasta Golf Club", address: "639 Achasta Dr, Dahlonega, GA 30533" },
    { name: "Country Club of Columbus", address: "2610 Cherokee Ave, Columbus, GA 31906" },
    { name: "The Georgia Club", address: "1050 Chancellors Dr, Statham, GA 30666" },
    { name: "Kinderlou Forest Golf Club", address: "4005 Bear Lake Rd, Valdosta, GA 31601" },
    { name: "The River Club", address: "1138 Crescent River Pass NW, Suwanee, GA 30024" },
    { name: "Barnsley Resort", address: "597 Barnsley Gardens Rd, Adairsville, GA 30103" },
    { name: "Crystal Falls Lake and Golf Club", address: "416 Crystal Falls Fairway, Dawsonville, GA 30534" },
    { name: "Legacy Golf Links & Driving Range", address: "1825 Windy Hill Rd SE, Smyrna, GA 30080" },
    { name: "The Club at Savannah Harbor", address: "2 Resort Dr, Savannah, GA 31421" },
    { name: "Hawks Ridge Golf Club", address: "1100 Hawks Ridge Dr, Ball Ground, GA 30107" },
    { name: "Piedmont Driving Club", address: "1215 Piedmont Ave NE, Atlanta, GA 30309" },
    { name: "The Ford Plantation", address: "12511 Ford Ave, Richmond Hill, GA 31324" },
    { name: "Capital City Club - Crabapple", address: "13802 New Providence Rd, Milton, GA 30004" },
    { name: "The Landings Club - Marshwood", address: "1 Cottonwood Ln, Savannah, GA 31411" },
    { name: "Peachtree Golf Club", address: "4600 Peachtree Rd, Atlanta, GA 30319" },
    { name: "Ocean Forest Golf Club", address: "100 Ocean Forest Dr, Sea Island, GA 31561" },
    { name: "Collins Hill Golf Club", address: "585 Camp Perrin Rd NE, Lawrenceville, GA 30043" },
    { name: "Trophy Club of Apalachee", address: "1008 Dacula Rd, Dacula, GA 30019" }, 
    { name: "Reunion Country Club", address: "5609 Grand Reunion Dr, Hoschton, GA 30548" },
    { name: "Stone Mountain Golf Club", address: "1145 Stonewall Jackson Dr, Stone Mountain, GA 30083" },
    { name: "Country Club of Gwinnett", address: "3254 Clubside View Ct SW, Snellville, GA 30039" },
    { name: "Cherokee Run Golf Club", address: "1595 Centennial Olympic Pkwy NE, Conyers, GA 30013" },
    { name: "Smoke Rise Country Club", address: "4900 Chedworth Dr, Stone Mountain, GA 30087" },
    { name: "Echelon Golf Club", address: "501 Founders Dr E, Alpharetta, GA 30004" },
    { name: "Bear's Best Atlanta", address: "5342 Aldeburgh Dr, Suwanee, GA 30024" },
    { name: "East Lake Golf Club", address: "2575 Alston Dr SE, Atlanta, GA 30317" },
    { name: "Augusta National Golf Club", address: "2604 Washington Rd, Augusta, GA 30904" },
    { name: "TPC Sugarloaf", address: "2595 Sugarloaf Club Dr, Duluth, GA 30097" },
    { name: "Atlanta Athletic Club", address: "1930 Bobby Jones Dr, Johns Creek, GA 30097" },
    { name: "Sea Island Golf Club", address: "100 Cloister Dr, Sea Island, GA 31561" },
    { name: "Reynolds Lake Oconee", address: "100 Linger Longer Rd, Greensboro, GA 30642" },
    { name: "Atlanta Country Club", address: "500 Atlanta Country Club Dr, Marietta, GA 30067" },
    { name: "Golf Club of Georgia", address: "1 Golf Club Dr, Alpharetta, GA 30005" },
    { name: "Cherokee Town & Country Club", address: "665 Hightower Trail, Atlanta, GA 30350" },
    { name: "The Chimneys Golf Course", address: "338 Monroe Hwy, Winder, GA 30680" },
    { name: "Sugar Hill Golf Club", address: "6094 Suwanee Dam Rd, Sugar Hill, GA 30518" },
    { name: "St Marlo Country Club", address: "7755 St Marlo Country Club Pkwy, Duluth, GA 30097" },
    { name: "The Chateau Elan Golf Club", address: "6060 Golf Club Dr, Braselton, GA 30517" },
    { name: "Chicopee Woods Golf Course", address: "2515 Atlanta Hwy, Gainesville, GA 30504" },
    { name: "Hamilton Mill Golf Club", address: "1995 Hamilton Mill Pkwy, Dacula, GA 30019" },
    { name: "Lanier Islands Legacy Golf Course", address: "7000 Lanier Islands Pkwy, Buford, GA 30518" },
    { name: "Pine Hills Golf Club", address: "2745 Pine Hills Dr, Winder, GA 30680" },
    { name: "Royal Lakes Golf & Country Club", address: "4700 Royal Lakes Dr, Flowery Branch, GA 30542" },
    { name: "Traditions of Braselton Golf Club", address: "401 Traditions Way, Jefferson, GA 30549" }
];


// Update the recommendClub function
function recommendClub(distance) {
    const gender = document.getElementById('player-gender').value;
    const level = document.getElementById('player-level').value;
    
    // Define distances for different player types
    const distances = {
        M: {
            pro: {
                driver: 280, threeWood: 260, fiveWood: 240,
                fourIron: 210, fiveIron: 200, sixIron: 190,
                sevenIron: 180, eightIron: 170, nineIron: 160,
                pitchingWedge: 140, gapWedge: 120, sandWedge: 100
            },
            amateur: {
                driver: 230, threeWood: 210, fiveWood: 190,
                fourIron: 170, fiveIron: 160, sixIron: 150,
                sevenIron: 140, eightIron: 130, nineIron: 120,
                pitchingWedge: 110, gapWedge: 90, sandWedge: 80
            },
            beginner: {
                driver: 180, threeWood: 170, fiveWood: 160,
                fourIron: 150, fiveIron: 140, sixIron: 130,
                sevenIron: 120, eightIron: 110, nineIron: 100,
                pitchingWedge: 90, gapWedge: 80, sandWedge: 70
            }
        },
        F: {
            pro: {
                driver: 250, threeWood: 230, fiveWood: 210,
                fourIron: 190, fiveIron: 180, sixIron: 170,
                sevenIron: 160, eightIron: 150, nineIron: 140,
                pitchingWedge: 120, gapWedge: 100, sandWedge: 80
            },
            amateur: {
                driver: 200, threeWood: 180, fiveWood: 170,
                fourIron: 150, fiveIron: 140, sixIron: 130,
                sevenIron: 120, eightIron: 110, nineIron: 100,
                pitchingWedge: 90, gapWedge: 80, sandWedge: 70
            },
            beginner: {
                driver: 150, threeWood: 140, fiveWood: 130,
                fourIron: 120, fiveIron: 110, sixIron: 100,
                sevenIron: 90, eightIron: 80, nineIron: 70,
                pitchingWedge: 60, gapWedge: 50, sandWedge: 40
            }
        }
    };

    const playerDistances = distances[gender][level];

    if (distance >= playerDistances.driver) return "Driver (드라이버)";
    if (distance >= playerDistances.threeWood) return "3 Wood (3번 우드)";
    if (distance >= playerDistances.fiveWood) return "5 Wood (5번 우드)";
    if (distance >= playerDistances.fourIron) return "4 Iron (4번 아이언)";
    if (distance >= playerDistances.fiveIron) return "5 Iron (5번 아이언)";
    if (distance >= playerDistances.sixIron) return "6 Iron (6번 아이언)";
    if (distance >= playerDistances.sevenIron) return "7 Iron (7번 아이언)";
    if (distance >= playerDistances.eightIron) return "8 Iron (8번 아이언)";
    if (distance >= playerDistances.nineIron) return "9 Iron (9번 아이언)";
    if (distance >= playerDistances.pitchingWedge) return "Pitching Wedge (피칭 웨지)";
    if (distance >= playerDistances.gapWedge) return "Gap Wedge (갭 웨지)";
    if (distance >= playerDistances.sandWedge) return "Sand Wedge (샌드 웨지)";
    return "Lob Wedge (롭 웨지)";
}


function initMap() {
    // 기본 좌표 (예: 미국 골프장 중심부)
    const defaultLocation = { lat: 34.0887, lng: -84.0989 };    

    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 17,
        mapTypeId: 'satellite',
        tilt: 0,  // 0도 각도로 기울기 설정
        heading: 0, // 초기 방향 설정
        mapTypeControl: true,
        fullscreenControl: true
    });
    // tilt가 안 먹힐때 맵이 완전히 로드된 후 tilt 강제 적용, 하지만 여전히 일부 지역에서는 기술적 제한으로 인해 작동하지 않을 수 있습니다.
    google.maps.event.addListenerOnce(map, 'idle', function() {
        map.setTilt(0);
    });


    // Add golf course markers
    const golfMarkers = [];
    georgiaGolfCourses.forEach(course => {
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ address: course.address }, function(results, status) {
            if (status === 'OK') {
                const marker = new google.maps.Marker({
                    map: map,
                    position: results[0].geometry.location,
                    title: course.name,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 8,
                        fillColor: '#4CAF50',
                        fillOpacity: 0.8,
                        strokeColor: '#ffffff',
                        strokeWeight: 2
                    }
                });

                const infowindow = new google.maps.InfoWindow({
                    content: `
                        <div style="padding: 10px;">
                            <strong>${course.name}</strong>
                            <br>
                            <button onclick="selectGolfCourse('${course.address}')" 
                                    style="margin-top: 8px; padding: 5px 10px; 
                                    background: #4CAF50; color: white; 
                                    border: none; border-radius: 3px; 
                                    cursor: pointer;">
                                이 골프장 선택
                            </button>
                        </div>`
                });

                marker.addListener('click', () => {
                    infowindow.open(map, marker);
                });

                golfMarkers.push(marker);
            }
        });
    });


    // Geocoder 객체 생성
    const geocoder = new google.maps.Geocoder();
    // Places Autocomplete 설정
    const input = document.getElementById('address-input');
    const autocomplete = new google.maps.places.Autocomplete(input);
    // 장소가 선택되었을 때의 이벤트 처리
    autocomplete.addListener('place_changed', function() {
        const place = autocomplete.getPlace();
        
        if (!place.geometry) {
            alert('선택된 장소의 정보를 찾을 수 없습니다.');
            return;
        }

        // 지도 중심을 선택된 장소로 이동
        map.setCenter(place.geometry.location);
        clearMarkers();
    });
    

    // 현재 위치 가져오기
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                map.setCenter(pos);
            },
            () => {
                // 위치 정보를 가져올 수 없는 경우 기본 위치 사용
                map.setCenter(defaultLocation);
                console.log('Geolocation failed');
            }
        );
    } else {
        // 브라우저가 위치 정보를 지원하지 않는 경우 기본 위치 사용
        map.setCenter(defaultLocation);
        console.log('Browser does not support geolocation');
    }

    // 클릭 이벤트 리스너
    map.addListener('click', function(e) {
        if (markers.length < 2) {
            placeMarker(e.latLng);
        }
    });

    polyline = new google.maps.Polyline({
        path: [],
        strokeColor: '#FF0000',
        strokeOpacity: 1.0,
        strokeWeight: 2,
        map: map
    });
    populateGolfCourses(); // Add this line at the end of initMap
}

// Add this new function after the existing code
function selectGolfCourse(address) {
    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ address: address }, function(results, status) {
        if (status === 'OK') {
            map.setCenter(results[0].geometry.location);
            map.setZoom(17);
            map.setMapTypeId('satellite');
            clearMarkers();
        }
    });
}


function placeMarker(location) {
    const markerOptions = {
        position: location,
        map: map
    };
    
    // 첫 번째 마커는 파란색, 두 번째 마커는 빨간색으로 설정
    if (isFirstMarker) {
        markerOptions.icon = {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 10,
            fillColor: '#4285F4',
            fillOpacity: 1,
            strokeColor: '#ffffff',
            strokeWeight: 2
        };
        markerOptions.title = '시작 위치';
    } else {
        markerOptions.icon = {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 10,
            fillColor: '#FF0000',
            fillOpacity: 1,
            strokeColor: '#ffffff',
            strokeWeight: 2
        };
        markerOptions.title = '타겟 위치';
    }

    const marker = new google.maps.Marker(markerOptions);
    markers.push(marker);

    // 선 그리기
    const path = polyline.getPath();
    path.push(location);

    // 거리 계산과 InfoWindow 표시
    if (markers.length > 1) {
        const distance = google.maps.geometry.spherical.computeDistanceBetween(
            markers[0].getPosition(),
            markers[1].getPosition()
        );
        
        // 미터를 야드로 변환
        const yards = Math.round(distance * 1.0936);
        totalDistance = yards;
        
        // 이전 InfoWindow가 있다면 닫기
        if (infoWindow) {
            infoWindow.close();
        }

        // 새로운 InfoWindow 생성
        const contentString = `
        <div style="padding: 10px;">
        <p style="margin: 0 0 8px 0;">거리: ${totalDistance} yards</p>
        <p style="margin: 0 0 8px 0;">추천 클럽: ${recommendClub(totalDistance)}</p>
        <button onclick="clearMarkers()" style="padding: 5px 10px; background: #4CAF50; color: white; border: none; border-radius: 3px; cursor: pointer;">초기화</button>
    </div>
        `;

        infoWindow = new google.maps.InfoWindow({
            content: contentString
        });

        // InfoWindow를 두 번째 마커(타겟) 위치에 표시
        infoWindow.open(map, marker);

        // InfoWindow가 닫힐 때 초기화 실행
        infoWindow.addListener('closeclick', function() {
            clearMarkers();
        });
    }

    isFirstMarker = false;
}

function clearMarkers() {
    for (let marker of markers) {
        marker.setMap(null);
    }
    markers = [];
    polyline.setPath([]);
    totalDistance = 0;
    isFirstMarker = true;
    document.getElementById('total-distance').innerHTML = '0';

    // InfoWindow도 닫기
    if (infoWindow) {
        infoWindow.close();
        infoWindow = null;
    }
}
// 주소 검색 함수 추가
/*
function searchLocation() {
    const input = document.getElementById('address-input');
    const autocomplete = new google.maps.places.Autocomplete(input);
    
    const place = autocomplete.getPlace();
    if (place && place.geometry) {
        map.setCenter(place.geometry.location);
        clearMarkers();
    }
}
*/
function searchLocation() {
    const input = document.getElementById('address-input');
    const geocoder = new google.maps.Geocoder();
    
    geocoder.geocode({ address: input.value }, function(results, status) {
        if (status === 'OK') {
            map.setCenter(results[0].geometry.location);
            clearMarkers();
        } else {
            alert('주소를 찾을 수 없습니다: ' + status);
        }
    });
}

//Add Swing Analysis Section script
let isDrawingMode = false;
let canvas, ctx;
let videoPlayer;

document.getElementById('upload-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const file = document.getElementById('swing-video').files[0];
    if (file) {
        const videoUrl = URL.createObjectURL(file);
        videoPlayer = document.getElementById('swing-video-player');
        videoPlayer.src = videoUrl;
        
        document.getElementById('video-container').style.display = 'block';
        
        // Initialize canvas after video loads
        videoPlayer.addEventListener('loadedmetadata', function() {
            initCanvas();
        });
    }
});

function playVideo() {
    if (videoPlayer) {
        videoPlayer.play();
    }
}

function pauseVideo() {
    if (videoPlayer) {
        videoPlayer.pause();
    }
}

function closeVideo() {
    document.getElementById('video-container').style.display = 'none';
    if (videoPlayer) {
        videoPlayer.pause();
        videoPlayer.src = '';
    }
    if (canvas) {
        clearCanvas();
    }
}

// Update the file upload handler
document.getElementById('upload-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const file = document.getElementById('swing-video').files[0];
    if (file) {
        const videoUrl = URL.createObjectURL(file);
        videoPlayer = document.getElementById('swing-video-player');
        videoPlayer.src = videoUrl;
        videoPlayer.load(); // Add this line to force video reload
        
        document.getElementById('video-container').style.display = 'block';
        
        // Initialize canvas after video loads
        videoPlayer.addEventListener('loadedmetadata', function() {
            initCanvas();
            // Add this to ensure video is ready to play
            videoPlayer.addEventListener('canplay', function() {
                console.log('Video can play');
            });
        });
    }
});

function initCanvas() {
    canvas = document.getElementById('analysis-canvas');
    videoPlayer = document.getElementById('swing-video-player');
    
    // Set canvas size to match video dimensions
    canvas.width = videoPlayer.videoWidth;
    canvas.height = videoPlayer.videoHeight;
    
    // Position canvas exactly over the video
    canvas.style.position = 'absolute';
    canvas.style.left = videoPlayer.offsetLeft + 'px';
    canvas.style.top = videoPlayer.offsetTop + 'px';
    canvas.style.width = videoPlayer.offsetWidth + 'px';
    canvas.style.height = videoPlayer.offsetHeight + 'px';
    
    ctx = canvas.getContext('2d');
    
    // Add event listeners for both mouse and touch events
    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseleave', stopDrawing);
    
    // Add touch events
    canvas.addEventListener('touchstart', handleTouch);
    canvas.addEventListener('touchmove', handleTouch);
    canvas.addEventListener('touchend', stopDrawing);
    
    // Update canvas position when video size changes
    window.addEventListener('resize', function() {
        canvas.style.left = videoPlayer.offsetLeft + 'px';
        canvas.style.top = videoPlayer.offsetTop + 'px';
        canvas.style.width = videoPlayer.offsetWidth + 'px';
        canvas.style.height = videoPlayer.offsetHeight + 'px';
    });
}
// Add touch event handler
function handleTouch(e) {
    e.preventDefault();
    const touch = e.touches[0];
    const mouseEvent = new MouseEvent(e.type === 'touchstart' ? 'mousedown' : 'mousemove', {
        clientX: touch.clientX,
        clientY: touch.clientY
    });
    canvas.dispatchEvent(mouseEvent);
}

function getMousePos(canvas, evt) {
    const rect = canvas.getBoundingClientRect();
    const scaleX = canvas.width / rect.width;
    const scaleY = canvas.height / rect.height;
    
    return {
        x: (evt.clientX - rect.left) * scaleX,
        y: (evt.clientY - rect.top) * scaleY
    };
}

let isDrawing = false;
let lastX = 0;
let lastY = 0;

function startDrawing(e) {
    if (!isDrawingMode) return;
    isDrawing = true;
    const pos = getMousePos(canvas, e);
    [lastX, lastY] = [pos.x, pos.y];
}

function draw(e) {
    if (!isDrawing || !isDrawingMode) return;
    e.preventDefault(); // Prevent scrolling while drawing
    const pos = getMousePos(canvas, e);
    
    ctx.beginPath();
    ctx.moveTo(lastX, lastY);
    ctx.lineTo(pos.x, pos.y);
    ctx.strokeStyle = '#FF0000';
    ctx.lineWidth = 2;
    ctx.stroke();
    [lastX, lastY] = [pos.x, pos.y];
}

function stopDrawing() {
    isDrawing = false;
}

function toggleDrawing() {
    isDrawingMode = !isDrawingMode;
    canvas.style.cursor = isDrawingMode ? 'crosshair' : 'default';
    
    // Add visual feedback
    const button = document.querySelector('button[onclick="toggleDrawing()"]');
    if (isDrawingMode) {
        button.style.backgroundColor = '#FF0000';
        button.textContent = '선 그리기 중지';
    } else {
        button.style.backgroundColor = '#4CAF50';
        button.textContent = '선 그리기';
    }
}

function clearCanvas() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}

function prevFrame() {
    videoPlayer.currentTime = Math.max(videoPlayer.currentTime - 0.04, 0);
}

function nextFrame() {
    videoPlayer.currentTime = Math.min(videoPlayer.currentTime + 0.04, videoPlayer.duration);
}
//Add Swing Analysis Section script


</script>

<!-- Google Maps API 로드 -->
<script async defer
src="https://maps.googleapis.com/maps/api/js?key=YOUR-GOOGLE-API&libraries=geometry,places&callback=initMap">
</script>


<style>
#player-options select {
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    background-color: white;
}

#player-options select:focus {
    outline: none;
    border-color: #4CAF50;
}
#search-box {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 15px;
}

#address-input {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
}

#search-box button {
    width: 100%;
    padding: 12px;
    background: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}

.mobile-notice {
    padding: 10px;
    background: #f8f8f8;
    border-radius: 4px;
    margin-bottom: 10px;
    font-size: 14px;
    text-align: center;
}

@media screen and (min-width: 768px) {
    #golf-distance-container {
        max-width: 1200px;
        padding: 15px;
    }

    #search-box {
        flex-direction: row;
    }

    #search-box button {
        width: auto;
    }

    #map {
        height: 500px !important;
    }
}

#search-box button:hover {
    background: #45a049;
}

#golf-distance-container {
    margin: 10px auto;
    max-width: 100%;
    padding: 10px;
    touch-action: pan-y;
    -ms-touch-action: pan-y;
}

#distance-info {
    margin-top: 15px;
    padding: 10px;
    background: #f5f5f5;
    border-radius: 5px;
}

#distance-info button {
    padding: 5px 15px;
    background: #4CAF50;
    color: white;
    border: none;
    border-radius: 3px;
    cursor: pointer;
}

#distance-info button:hover {
    background: #45a049;
}

#golf-course-select {
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    background-color: white;
    margin-bottom: 15px;
}

#golf-course-select:focus {
    outline: none;
    border-color: #4CAF50;
}

#map {
    height: 400px !important; 
    width: 100%;
    touch-action: none !important;
    -ms-touch-action: none !important;
    user-select: none;
    -webkit-user-select: none;
}

.gm-style {
    touch-action: none !important;
    -ms-touch-action: none !important;
}

#scoring-system input[type="number"] {
    width: 50px;
    padding: 5px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

#scoring-system input[type="number"]:focus {
    outline: none;
    border-color: #4CAF50;
}

#rankings-list li {
    margin: 5px 0;
    padding: 8px;
    background: white;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

#distance-converter {
    margin-bottom: 20px;
    padding: 15px;
    background: #f5f5f5;
    border-radius: 5px;
}

#distance-converter h4 {
    margin-bottom: 15px;
    font-size: 16px;
}

#distance-converter .converter-inputs {
    display: flex;
    gap: 10px;
    flex-direction: column;
}

#distance-converter input {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
    box-sizing: border-box;
}

#distance-converter label {
    display: block;
    margin-top: 5px;
    font-size: 14px;
    color: #666;
}

/* Media query for tablets and larger screens */
@media screen and (min-width: 768px) {
    #distance-converter .converter-inputs {
        flex-direction: row;
    }
    
    #distance-converter input {
        max-width: 200px;
    }
}

#video-container {
    position: relative;
    width: 100%;
    max-width: 800px;
    margin: 0 auto;
    overflow: hidden;
}

#swing-video-player {
    width: 100%;
    display: block;
}

#analysis-canvas {
    position: absolute;
    top: 0;
    left: 0;
    pointer-events: auto;
    z-index: 2;
}

</style>

<?php
include_once(G5_PATH.'/tail.php');
?>