<?php
header('Content-Type: application/json; charset=utf-8');

if (!isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => '액션이 지정되지 않았습니다.']);
    exit;
}

$action = $_POST['action'];
$response = ['success' => false, 'message' => '', 'data' => null];

try {
    switch ($action) {
        case 'status':
            // PM2 명령어 존재 여부 확인
            $which_output = shell_exec('where pm2 2>&1'); // Windows
            if ($which_output === null || strpos($which_output, 'Could not find') !== false) {
                // Linux/Mac 방식도 시도
                $which_output = shell_exec('which pm2 2>&1');
                if ($which_output === null || empty(trim($which_output))) {
                    $response['message'] = 'PM2 명령어를 찾을 수 없습니다. PM2가 설치되지 않았거나 환경변수 PATH에 등록되지 않았습니다.';
                    $response['data'] = [];
                    $response['pm2_not_found'] = true;
                    break;
                }
            }
            
            $output = shell_exec('pm2 jlist 2>&1');
            if ($output === null) {
                $response['message'] = 'PM2 명령어 실행 실패 - 권한 문제이거나 시스템 오류가 발생했습니다.';
                $response['data'] = [];
            } else {
                $trimmed_output = trim($output);
                
                // PM2가 설치되지 않았거나 찾을 수 없는 경우 (다양한 OS별 메시지 처리)
                if (strpos($output, "'pm2' is not recognized") !== false || 
                    strpos($output, "'pm2'은(는) 내부 또는 외부 명령") !== false ||
                    strpos($output, "command not found") !== false ||
                    strpos($output, "No such file") !== false ||
                    strpos($output, "pm2: not found") !== false ||
                    strpos($output, "bash: pm2:") !== false ||
                    strpos($output, "sh: pm2:") !== false ||
                    empty($trimmed_output)) {
                    
                    $response['message'] = 'PM2가 설치되지 않았거나 환경변수에 등록되지 않았습니다.';
                    $response['data'] = [];
                    $response['pm2_not_found'] = true;
                } 
                // PM2 데몬이 실행되지 않은 경우
                else if (strpos($output, 'daemon not launched') !== false ||
                         strpos($output, 'God Daemon') !== false ||
                         strpos($output, 'PM2 not launched') !== false) {
                    $response['success'] = true;
                    $response['data'] = [];
                    $response['message'] = 'PM2 데몬이 실행되지 않았습니다. 프로세스가 없습니다.';
                }
                // PM2가 실행 중이지만 프로세스 목록이 비어있는 경우
                else if ($trimmed_output === '[]' || $trimmed_output === '') {
                    $response['success'] = true;
                    $response['data'] = [];
                    $response['message'] = 'PM2는 실행 중이지만 관리되는 프로세스가 없습니다.';
                }
                // 정상적인 JSON 응답인 경우
                else {
                    $processes = json_decode($output, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($processes)) {
                        $response['success'] = true;
                        $response['data'] = $processes;
                        if (empty($processes)) {
                            $response['message'] = 'PM2는 실행 중이지만 관리되는 프로세스가 없습니다.';
                        }
                    } else {
                        $response['message'] = 'PM2 상태 조회 실패 - 응답 형식이 올바르지 않습니다: ' . $output;
                        $response['data'] = [];
                    }
                }
            }
            break;

        case 'start':
            if (!isset($_POST['name'])) {
                $response['message'] = '프로세스 이름이 지정되지 않았습니다.';
                break;
            }
            
            // hnsLpr, hnslpr 프로세스 시도
            $processNames = ['hnsLpr', 'hnslpr'];
            $success = false;
            $lastOutput = '';
            
            foreach ($processNames as $processName) {
                $name = escapeshellarg($processName);
                $output = shell_exec("pm2 start {$name} 2>&1");
                $lastOutput = $output;
                
                if ($output === null) {
                    continue;
                } else if (strpos($output, "'pm2' is not recognized") !== false || 
                           strpos($output, "command not found") !== false) {
                    $response['message'] = 'PM2가 설치되지 않았습니다.';
                    break;
                } else if (strpos($output, 'started') !== false || strpos($output, 'online') !== false) {
                    $response['success'] = true;
                    $response['message'] = "HnsLpr 서비스({$processName}) 시작 완료";
                    $success = true;
                    break;
                }
            }
            
            if (!$success && !isset($response['message'])) {
                $response['message'] = "HnsLpr 서비스 시작 실패: {$lastOutput}";
            }
            break;

        case 'stop':
            if (!isset($_POST['name'])) {
                $response['message'] = '프로세스 이름이 지정되지 않았습니다.';
                break;
            }
            
            // 현재 실행 중인 hnsLpr 프로세스를 찾아서 중지
            $listOutput = shell_exec('pm2 jlist 2>&1');
            if ($listOutput && $listOutput !== null) {
                $processes = json_decode($listOutput, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($processes)) {
                    $hnsLprProcess = null;
                    foreach ($processes as $proc) {
                        $procName = strtolower($proc['name']);
                        // hnsLpr, hnslpr 대소문자 구분 없이 찾기
                        if ($procName === 'hnsLpr' || $procName === 'hnslpr') {
                            $hnsLprProcess = $proc['name'];
                            break;
                        }
                    }
                    
                    if ($hnsLprProcess) {
                        $name = escapeshellarg($hnsLprProcess);
                        $output = shell_exec("pm2 stop {$name} 2>&1");
                        
                        if (strpos($output, 'stopped') !== false) {
                            $response['success'] = true;
                            $response['message'] = "HnsLpr 서비스({$hnsLprProcess}) 중지 완료";
                        } else {
                            $response['message'] = "HnsLpr 서비스 중지 실패: {$output}";
                        }
                    } else {
                        $response['message'] = 'HnsLpr 서비스를 찾을 수 없습니다.';
                    }
                } else {
                    $response['message'] = 'PM2 프로세스 목록 조회 실패';
                }
            } else {
                $response['message'] = 'PM2 명령어 실행 실패';
            }
            break;

        case 'restart':
            if (!isset($_POST['name'])) {
                $response['message'] = '프로세스 이름이 지정되지 않았습니다.';
                break;
            }
            
            // 현재 실행 중인 hnsLpr 프로세스를 찾아서 재시작
            $listOutput = shell_exec('pm2 jlist 2>&1');
            if ($listOutput && $listOutput !== null) {
                $processes = json_decode($listOutput, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($processes)) {
                    $hnsLprProcess = null;
                    foreach ($processes as $proc) {
                        $procName = strtolower($proc['name']);
                        // hnsLpr, hnslpr 대소문자 구분 없이 찾기
                        if ($procName === 'hnsLpr' || $procName === 'hnslpr') {
                            $hnsLprProcess = $proc['name'];
                            break;
                        }
                    }
                    
                    if ($hnsLprProcess) {
                        $name = escapeshellarg($hnsLprProcess);
                        $output = shell_exec("pm2 restart {$name} 2>&1");
                        
                        if (strpos($output, 'restarted') !== false || strpos($output, 'online') !== false) {
                            $response['success'] = true;
                            $response['message'] = "HnsLpr 서비스({$hnsLprProcess}) 재시작 완료";
                        } else {
                            $response['message'] = "HnsLpr 서비스 재시작 실패: {$output}";
                        }
                    } else {
                        // 프로세스가 없으면 시작 시도
                        $processNames = ['hnsLpr', 'hnslpr'];
                        $success = false;
                        
                        foreach ($processNames as $processName) {
                            $name = escapeshellarg($processName);
                            $output = shell_exec("pm2 start {$name} 2>&1");
                            
                            if (strpos($output, 'started') !== false || strpos($output, 'online') !== false) {
                                $response['success'] = true;
                                $response['message'] = "HnsLpr 서비스({$processName}) 시작 완료";
                                $success = true;
                                break;
                            }
                        }
                        
                        if (!$success) {
                            $response['message'] = 'HnsLpr 서비스를 찾을 수 없어서 시작할 수 없습니다.';
                        }
                    }
                } else {
                    $response['message'] = 'PM2 프로세스 목록 조회 실패';
                }
            } else {
                $response['message'] = 'PM2 명령어 실행 실패';
            }
            break;

        case 'reload':
            $output = shell_exec('pm2 reload all 2>&1');
            
            if ($output === null) {
                $response['message'] = 'PM2 명령어 실행 실패';
            } else if (strpos($output, "'pm2' is not recognized") !== false || 
                       strpos($output, "command not found") !== false) {
                $response['message'] = 'PM2가 설치되지 않았습니다.';
            } else if (strpos($output, 'successfully') !== false || strpos($output, 'reloaded') !== false) {
                $response['success'] = true;
                $response['message'] = '모든 프로세스 리로드 완료';
            } else {
                $response['message'] = "프로세스 리로드 실패: {$output}";
            }
            break;

        case 'logs':
            // 현재 실행 중인 hnsLpr 프로세스를 찾아서 로그 조회
            $listOutput = shell_exec('pm2 jlist 2>&1');
            if ($listOutput && $listOutput !== null) {
                $processes = json_decode($listOutput, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($processes)) {
                    $hnsLprProcess = null;
                    foreach ($processes as $proc) {
                        $procName = strtolower($proc['name']);
                        // hnsLpr, hnslpr 대소문자 구분 없이 찾기
                        if ($procName === 'hnslpr' || $procName === 'hnslpr') {
                            $hnsLprProcess = $proc['name'];
                            break;
                        }
                    }
                    
                    if ($hnsLprProcess) {
                        $name = escapeshellarg($hnsLprProcess);
                        $output = shell_exec("pm2 logs {$name} --lines 10 --nostream 2>&1");
                        
                        if ($output === null) {
                            $response['message'] = 'PM2 로그 명령어 실행 실패';
                        } else if (strpos($output, "'pm2' is not recognized") !== false || 
                                   strpos($output, "command not found") !== false) {
                            $response['message'] = 'PM2가 설치되지 않았습니다.';
                        } else {
                            $response['success'] = true;
                            $response['data'] = $output;
                        }
                    } else {
                        $response['message'] = 'HnsLpr 프로세스를 찾을 수 없습니다.';
                    }
                } else {
                    $response['message'] = 'PM2 프로세스 목록 조회 실패';
                }
            } else {
                $response['message'] = 'PM2 명령어 실행 실패';
            }
            break;

        default:
            $response['message'] = '알 수 없는 액션입니다.';
            break;
    }
} catch (Exception $e) {
    $response['message'] = 'PM2 제어 중 오류 발생: ' . $e->getMessage();
}

echo json_encode($response);
?>