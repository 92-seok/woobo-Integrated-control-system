<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';
?>

<!-- PM2 서비스 관리 모듈 -->
<style>
    .pm2-container {
        padding: 20px;
        font-family: 'Nanum Square', sans-serif;
    }

    .pm2-header {
        border-bottom: 2px solid #4a90e2;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .pm2-header h2 {
        color: #333;
        margin: 0;
        font-size: 24px;
    }

    .control-panel {
        margin-bottom: 20px;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 5px;
        border: 1px solid #dee2e6;
    }

    .btn-group {
        margin-bottom: 15px;
    }

    .btn {
        padding: 8px 16px;
        margin: 0 5px 5px 0;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
        transition: background-color 0.3s;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
    }

    .btn-success {
        background-color: #28a745;
        color: white;
    }

    .btn-warning {
        background-color: #ffc107;
        color: #212529;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    .process-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        background-color: white;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
    }

    .process-table th,
    .process-table td {
        padding: 12px 8px;
        text-align: left;
        border-bottom: 1px solid #dee2e6;
        font-size: 13px;
    }

    .process-table th {
        background-color: #f8f9fa;
        font-weight: bold;
        color: #495057;
    }

    .status-online {
        background-color: #d4edda;
        color: #155724;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
    }

    .status-stopped {
        background-color: #f8d7da;
        color: #721c24;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
    }

    .status-error {
        background-color: #ffeaa7;
        color: #6c5500;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
    }

    .logs-container {
        margin-top: 20px;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        background-color: #f8f9fa;
    }

    .logs-header {
        padding: 10px 15px;
        background-color: #e9ecef;
        border-bottom: 1px solid #dee2e6;
        font-weight: bold;
    }

    .logs-content {
        max-height: 300px;
        overflow-y: auto;
        padding: 15px;
        font-family: 'Consolas', 'Monaco', monospace;
        font-size: 12px;
        line-height: 1.4;
        background-color: #2d3748;
        color: #e2e8f0;
    }

    .loading {
        text-align: center;
        padding: 20px;
        font-style: italic;
        color: #6c757d;
    }

    .alert {
        padding: 12px;
        margin-bottom: 15px;
        border-radius: 4px;
        font-size: 13px;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .alert-warning {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    .pm2-not-available {
        text-align: center;
        padding: 40px 20px;
        background-color: #f8f9fa;
        border-radius: 5px;
        border: 2px dashed #dee2e6;
    }

    .pm2-not-available .icon {
        font-size: 48px;
        color: #6c757d;
        margin-bottom: 20px;
    }

    .pm2-not-available h3 {
        color: #495057;
        margin-bottom: 15px;
    }

    .pm2-not-available p {
        color: #6c757d;
        margin-bottom: 20px;
        line-height: 1.5;
    }

    .pm2-not-available .installation-guide {
        background-color: #e9ecef;
        padding: 15px;
        border-radius: 4px;
        margin-top: 15px;
        text-align: left;
    }

    .pm2-not-available .installation-guide h4 {
        margin-bottom: 10px;
        color: #495057;
    }

    .pm2-not-available .installation-guide code {
        background-color: #f8f9fa;
        padding: 2px 4px;
        border-radius: 3px;
        font-family: monospace;
    }
    </style>
    <div class="pm2-container">
        <div class="pm2-header">
            <h2>HnsLpr 서비스 관리</h2>
        </div>

        <div class="control-panel">
            <div class="btn-group">
                <button class="btn btn-primary" onclick="refreshStatus()">새로고침</button>
                <button class="btn btn-success" onclick="controlHnsLpr('start')" id="startBtn" style="display:none;">시작</button>
                <button class="btn btn-danger" onclick="controlHnsLpr('stop')" id="stopBtn" style="display:none;">중지</button>
                <button class="btn btn-warning" onclick="controlHnsLpr('restart')" id="restartBtn" style="display:none;">재시작</button>
                <button class="btn btn-secondary" onclick="toggleLogs()" id="logsBtn" style="display:none;">로그 보기</button>
            </div>

            <div id="messageArea"></div>
        </div>

        <div id="processStatusContainer">
            <div class="loading">HnsLpr 서비스 상태를 확인하는 중...</div>
        </div>
        
        <div class="logs-container" id="logsContainer" style="display: none; margin-top: 20px;">
            <div class="logs-header" style="padding: 10px 15px; background-color: #e9ecef; border-bottom: 1px solid #dee2e6; font-weight: bold; display: flex; justify-content: space-between; align-items: center;">
                <span>HnsLpr 실시간 로그 (최근 10줄)</span>
                <button class="btn btn-secondary" style="padding: 2px 8px; font-size: 11px;" onclick="toggleLogs()">닫기</button>
            </div>
            <div class="logs-content" id="logsContent" style="max-height: 300px; overflow-y: auto; padding: 15px; font-family: 'Consolas', 'Monaco', monospace; font-size: 12px; line-height: 1.4; background-color: #2d3748; color: #e2e8f0;"></div>
        </div>
    </div>

<script>
    let autoRefreshInterval;

    // jQuery가 이미 로드되어 있다고 가정 (parking 모듈에서)
    $(document).ready(function() {
        refreshStatus();

        // 30초마다 자동 새로고침
        autoRefreshInterval = setInterval(refreshStatus, 30000);
    });

    function refreshStatus() {
        showMessage('상태를 새로고침하는 중...', 'info');

        $.ajax({
            url: '/parking/server/pm2Control.php',
            method: 'POST',
            data: {
                action: 'status'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    displayHnsLprStatus(response.data);
                    if (response.message) {
                        showMessage(response.message, 'warning');
                    } else {
                        hideMessage();
                    }
                } else {
                    showMessage(response.message, 'error');
                    displayPM2NotAvailable(response.message);
                }
            },
            error: function(xhr, status, error) {
                showMessage('서버 오류: ' + error, 'error');
                $('#processStatusContainer').html('<div class="loading">서버 연결에 실패했습니다.</div>');
            }
        });
    }

    function displayPM2NotAvailable(message) {
        let html = '<div class="pm2-not-available">';
        html += '<div style="margin-bottom: 20px;"><span style="display: inline-block; width: 60px; height: 60px; background: linear-gradient(135deg, #f44336, #d32f2f); border-radius: 50%; position: relative; box-shadow: 0 4px 15px rgba(244, 67, 54, 0.4);"><span style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 24px; font-weight: bold;">X</span></span></div>';
        html += '<h3>PM2 서비스를 사용할 수 없습니다</h3>';
        html += '<p>' + message + '</p>';

        if (message.includes('설치되지 않았습니다') || message.includes('찾을 수 없습니다') || message.includes('환경변수')) {
            html += '<div class="installation-guide">';
            html += '<h4>PM2 설치 및 설정 가이드:</h4>';
            html += '<p><strong>1. Node.js 확인:</strong></p>';
            html += '<p>먼저 Node.js가 설치되어 있는지 확인하세요: <code>node --version</code></p>';
            
            html += '<p><strong>2. PM2 전역 설치:</strong></p>';
            html += '<p><code>npm install -g pm2</code></p>';
            
            html += '<p><strong>3. Windows 서비스로 설치 (선택사항):</strong></p>';
            html += '<p><code>npm install -g pm2-windows-service</code></p>';
            html += '<p><code>pm2-service-install</code></p>';
            
            html += '<p><strong>4. 환경변수 설정 확인:</strong></p>';
            html += '<p>PM2가 설치되었지만 명령어를 찾을 수 없다면, Node.js의 전역 모듈 경로가 시스템 PATH에 포함되어 있는지 확인하세요.</p>';
            html += '<p>일반적으로 <code>C:\\Users\\[사용자명]\\AppData\\Roaming\\npm</code> 경로가 PATH에 있어야 합니다.</p>';
            
            html += '<p><strong>5. 서버 재시작:</strong></p>';
            html += '<p>PM2 설치 후 웹 서버(Apache)를 재시작해주세요.</p>';
            html += '</div>';
        } else if (message.includes('데몬')) {
            html += '<div class="installation-guide">';
            html += '<h4>PM2 데몬 시작 방법:</h4>';
            html += '<p>PM2는 설치되어 있지만 데몬이 실행되지 않았습니다.</p>';
            html += '<p>다음 명령어로 PM2를 시작하세요:</p>';
            html += '<p><code>pm2 list</code> 또는 <code>pm2 status</code></p>';
            html += '</div>';
        }

        html += '</div>';
        $('#processStatusContainer').html(html);
    }

    function displayHnsLprStatus(processes) {
        // hnsLpr, hnslpr 대소문자 구분 없이 찾기
        let hnsLprProcess = processes.find(proc => {
            let name = proc.name.toLowerCase();
            return name === 'hnsLpr' || name === 'hnslpr';
        });

        let html = '<div class="process-table" style="padding: 20px; text-align: center;">';
        
        if (!hnsLprProcess) {
            html += '<div class="pm2-not-available">';
            html += '<div style="margin-bottom: 20px;"><span style="display: inline-block; width: 60px; height: 60px; background: linear-gradient(135deg, #ff9800, #f57c00); border-radius: 50%; position: relative; box-shadow: 0 4px 15px rgba(255, 152, 0, 0.4);"><span style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 24px; font-weight: bold;">?</span></span></div>';
            html += '<h3>HnsLpr 서비스를 찾을 수 없습니다</h3>';
            html += '<p>PM2에서 관리되는 HnsLpr 프로세스가 없습니다.</p>';
            html += '</div>';
            hideControlButtons();
        } else {
            let status = hnsLprProcess.pm2_env.status;
            let statusIcon = status === 'online' ? 
                '<span style="display: inline-block; width: 60px; height: 60px; background: linear-gradient(135deg, #4CAF50, #45a049); border-radius: 50%; position: relative; box-shadow: 0 4px 15px rgba(76, 175, 80, 0.4);"><span style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 24px; font-weight: bold;">O</span></span>' :
                '<span style="display: inline-block; width: 60px; height: 60px; background: linear-gradient(135deg, #f44336, #d32f2f); border-radius: 50%; position: relative; box-shadow: 0 4px 15px rgba(244, 67, 54, 0.4);"><span style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 24px; font-weight: bold;">X</span></span>';
            let statusText = status === 'online' ? '실행 중' : '중지됨';
            let statusClass = status === 'online' ? 'status-online' : 'status-stopped';
            
            let uptime = hnsLprProcess.pm2_env.pm_uptime ? 
                formatUptime(Date.now() - hnsLprProcess.pm2_env.pm_uptime) : '-';
            
            html += '<div style="background: white; border-radius: 10px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">';
            html += '<div style="margin-bottom: 20px;">' + statusIcon + '</div>';
            html += '<h3>HnsLpr 서비스</h3>';
            html += '<div style="margin: 20px 0;">';
            html += '<span class="' + statusClass + '" style="font-size: 16px; padding: 5px 15px;">' + statusText + '</span>';
            html += '</div>';
            html += '<div style="margin-top: 20px; color: #666; font-size: 14px;">';
            html += '<p><strong>프로세스 이름:</strong> ' + hnsLprProcess.name + '</p>';
            html += '<p><strong>CPU 사용률:</strong> ' + (hnsLprProcess.monit.cpu || 0) + '%</p>';
            html += '<p><strong>메모리 사용량:</strong> ' + formatBytes(hnsLprProcess.monit.memory || 0) + '</p>';
            html += '<p><strong>재시작 횟수:</strong> ' + hnsLprProcess.pm2_env.restart_time + '회</p>';
            html += '<p><strong>가동 시간:</strong> ' + uptime + '</p>';
            html += '</div>';
            html += '</div>';
            
            showControlButtons(status);
        }
        
        html += '</div>';
        $('#processStatusContainer').html(html);
    }
    
    function showControlButtons(status) {
        $('#startBtn, #stopBtn, #restartBtn, #logsBtn').hide();
        
        if (status === 'online') {
            $('#stopBtn, #restartBtn, #logsBtn').show();
        } else {
            $('#startBtn, #restartBtn').show();
        }
    }
    
    function hideControlButtons() {
        $('#startBtn, #stopBtn, #restartBtn, #logsBtn').hide();
    }

    function controlHnsLpr(action) {
        let actionText = {
            'start': '시작',
            'stop': '중지', 
            'restart': '재시작'
        }[action];
        
        if (!confirm('HnsLpr 서비스를 ' + actionText + ' 하시겠습니까?')) {
            return;
        }
        
        showMessage('HnsLpr 서비스를 ' + actionText + ' 하는 중...', 'info');
        
        $.ajax({
            url: '/parking/server/pm2Control.php',
            method: 'POST',
            data: {
                action: action,
                name: 'hnsLpr'  // 또는 'hnslpr' - 서버에서 둘 다 시도하도록 수정 필요
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showMessage(response.message, 'success');
                    setTimeout(refreshStatus, 1000);
                } else {
                    showMessage('작업 실패: ' + response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                showMessage('서버 오류: ' + error, 'error');
            }
        });
    }
    
    let logsInterval;
    let logsVisible = false;
    
    function toggleLogs() {
        if (logsVisible) {
            $('#logsContainer').hide();
            $('#logsBtn').text('로그 보기');
            logsVisible = false;
            if (logsInterval) {
                clearInterval(logsInterval);
            }
        } else {
            $('#logsContainer').show();
            $('#logsBtn').text('로그 닫기');
            logsVisible = true;
            loadLogs();
            // 5초마다 로그 업데이트
            logsInterval = setInterval(loadLogs, 5000);
        }
    }
    
    function loadLogs() {
        $.ajax({
            url: '/parking/server/pm2Control.php',
            method: 'POST',
            data: {
                action: 'logs'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    let logs = response.data;
                    // 마지막 10줄만 표시
                    let lines = logs.split('\n').slice(-10).join('\n');
                    $('#logsContent').html('<pre style="margin: 0; white-space: pre-wrap;">' + lines + '</pre>');
                    // 스크롤을 맨 아래로
                    $('#logsContent').scrollTop($('#logsContent')[0].scrollHeight);
                } else {
                    $('#logsContent').html('<pre style="margin: 0; color: #ff6b6b;">로그 조회 실패: ' + response.message + '</pre>');
                }
            },
            error: function(xhr, status, error) {
                $('#logsContent').html('<pre style="margin: 0; color: #ff6b6b;">서버 오류: ' + error + '</pre>');
            }
        });
    }

    function showMessage(message, type) {
        let alertClass = type === 'error' ? 'alert-danger' :
            type === 'success' ? 'alert-success' :
            type === 'warning' ? 'alert-warning' : '';

        $('#messageArea').html('<div class="alert ' + alertClass + '">' + message + '</div>');

        if (type === 'success' || type === 'warning') {
            setTimeout(hideMessage, 5000);
        }
    }

    function hideMessage() {
        $('#messageArea').html('');
    }

    function formatUptime(milliseconds) {
        let seconds = Math.floor(milliseconds / 1000);
        let minutes = Math.floor(seconds / 60);
        let hours = Math.floor(minutes / 60);
        let days = Math.floor(hours / 24);

        if (days > 0) return days + '일 ' + (hours % 24) + '시간';
        if (hours > 0) return hours + '시간 ' + (minutes % 60) + '분';
        if (minutes > 0) return minutes + '분 ' + (seconds % 60) + '초';
        return seconds + '초';
    }

    function formatBytes(bytes) {
        if (bytes === 0) return '0 B';
        let k = 1024;
        let sizes = ['B', 'KB', 'MB', 'GB'];
        let i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // 페이지 종료 시 자동 새로고침 및 로그 인터벌 중지
    $(window).on('beforeunload', function() {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
        }
        if (logsInterval) {
            clearInterval(logsInterval);
        }
    });
</script>