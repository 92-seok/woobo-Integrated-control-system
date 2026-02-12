# 프로젝트 개요

## 🏗️ 시스템 아키텍처
- **백엔드**: PHP 8.2 (PDO, MySQL 연동)
- **프론트엔드**: HTML5, CSS3, JavaScript
- **데이터베이스**: MySQL
- **웹서버**: Apache (XAMPP)
- **외부 연동**: 카카오맵 API, CCTV 스트리밍
- **환경 설정**: `.env` 파일 기반 설정 관리

## 📁 주요 메뉴별 기능

### 1. Data (센서 데이터 관리)
**경로**: `/data`

#### 주요 기능
- **센서 타입별 데이터 수집 및 처리**
  - `01`: 강우량 (rain)
  - `02`: 수위 (water)
  - `03`: 변위 (dplace)
  - `04`: 토양수분 (soil)
  - `06`: 적설량 (snow)
  - `08`: 경사도 (tilt)
  - `21`: 홍수위 (flood)
- **기간별 데이터 조회** (일/월/년)
- **엑셀 리포트 생성**

#### 주요 파일
- `dataFrame.php`: 메인 프레임
- `table/Day.php, Month.php, Year.php`: 기간별 데이터 조회
- `table/excel/`: 엑셀 리포트 생성

---

### 2. Alert (경고/알림 시스템)
**경로**: `/alert`

#### 주요 기능
- **경고 기준 설정 및 관리**
- **실시간 알림 발송**
- **제어 명령 발송**
- **시나리오 기반 자동 알림**
- **발령 상황 관리**

#### 주요 파일
- `frame/criForm.php, criList.php`: 경고 기준 설정
- `frame/alertForm.php, alertList.php`: 알림 관리
- `frame/controlIssue.php, controlDetail.php`: 제어 명령
- `frame/setAlertEachScen.php`: 시나리오 설정

---

### 3. Broad (방송 시스템)
**경로**: `/broad`

#### 주요 기능
- **실시간 방송 송출**
- **방송 결과 모니터링**
- **수신처 그룹 관리**
- **방송 메시지 템플릿 관리**
- **방송 이력 관리**

#### 주요 파일
- `frame/broadForm.php`: 방송 송출
- `frame/broadResult.php`: 방송 결과
- `frame/group.php, cidList.php`: 수신처 관리
- `frame/mentForm.php, mentList.php`: 메시지 관리

---

### 4. Display (전광판 관리)
**경로**: `/display`

#### 주요 기능
- **전광판/디스플레이 장비 제어**
- **장비별 시나리오 설정**
- **그룹 시나리오 관리**
- **실시간 메시지 전송**
- **장비 상태 모니터링**

#### 주요 파일
- `frame/eachScenForm.php`: 개별 시나리오
- `frame/groupScenForm.php`: 그룹 시나리오
- `frame/eachEquList.php, setEquList.php`: 장비 관리
- `frame/sendEachScen.php`: 시나리오 전송

---

### 5. Gate (차단기 관리)
**경로**: `/gate`

#### 주요 기능
- **차단기 원격 제어**: open/close 명령을 통한 원격 차단기 제어
- **차단기 상태 실시간 모니터링**: 개폐 상태, 동작 상태 실시간 확인
- **수동/자동 제어 모드 설정**: 관리자 수동 제어 및 시나리오 기반 자동 제어
- **차량 출입 관리**: 출입차량 기록 및 통계 관리
- **차단기 이력 관리**: 제어 명령 및 상태 변화 이력 추적

#### 주요 파일
- **gate/ 폴더**
  - `gateFrame.php`: 차단기 관리 메인 프레임
  - `frame/passiveGate.php`: 실시간 차단기 상태 모니터링 및 수동 제어 인터페이스
  - `frame/gateList.php`: 차단기 제어 목록 (기간별 조회, 페이징)
  - `frame/check_status.php`: 차단기 상태 실시간 체크 API
  - `frame/update_gate_status.php`: 차단기 상태 업데이트 처리
- **js/ 폴더**
  - `gate.js`: 차단기 제어 메인 JavaScript
    - **1. initPassiveGate()** - 차단기 페이지 초기화 함수
      - **역할**: 차단기 페이지의 모든 이벤트 핸들러를 설정하는 메인 초기화 함수
      - **기능**:
        - 도움말 툴팁 클릭 이벤트 바인딩
        - 차단기 열림/닫힘 버튼 클릭 이벤트 바인딩
        - CCTV 버튼 클릭 이벤트 바인딩
    - **2. 차단기 제어 버튼 클릭 시 실행 흐름**
      - **2-1. cleanupCctv()** - 기존 스트림 정리 함수
        - **역할**: 현재 재생 중인 CCTV 스트림을 완전히 정리
        - **기능**:
          - 웹소켓 연결 종료
          - 캔버스 플레이어 정리
          - 타이머 정리
          - 비디오 요소 리셋
      - **2-2. initLowQualityGate(eType)** - 저품질 CCTV 스트림 시작
        - **역할**: 차단기 제어 시 저품질 CCTV 스트림을 시작
        - **기능**:
          - 플레이스홀더 숨기고 스트림 영역 표시
          - CCTV 데이터 로드
          - 해당 차단기에 연결된 CCTV 찾기
          - 저품질 스트림 URL 생성 및 재생
      - **2-3. loadCctvData(callback)** - CCTV 데이터 로드 함수
        - **역할**: 서버에서 CCTV 장비 정보를 가져와서 캐시에 저장
        - **기능**:
          - AJAX로 `/include/server/cctvData.php` 호출
          - CCTV 데이터를 `cctvDataCache`에 저장
          - 콜백 함수 실행
      - **2-4. saveGate(num, gate)** - 차단기 상태 저장 함수
        - **역할**: 차단기 상태 변경을 서버에 저장
        - **기능**:
          - AJAX로 `server/serverGate.php`에 POST 요청
          - 성공 시 로그 기록 및 알림 표시
          - 실패 시 에러 메시지 표시
    - **3. CCTV 버튼 클릭 시 실행 흐름**
      - **3-1. initLowQualityGate(eType)** - 저품질 스트림 시작
        - **역할**: CCTV 버튼 클릭 시 저품질 스트림 시작
        - **기능**: 차단기 제어와 동일한 저품질 스트림 로직
      - **3-2. cleanupCctv()** - 스트림 정리 (정지 버튼 클릭 시)
        - **역할**: CCTV 정지 버튼 클릭 시 스트림 정리
        - **기능**: 모든 스트림 리소스 정리 및 플레이스홀더 복귀
    - **4. 상태 업데이트 함수**
      - **4-1. updateButtonStates()** - 버튼 상태 업데이트 함수
        - **역할**: 서버에서 차단기 상태를 확인하고 UI 업데이트
        - **기능**:
          - `/gate/frame/check_status.php` 호출
          - 각 차단기 버튼의 활성 상태 업데이트
          - 상태 텍스트 업데이트
    - **5. 고품질 스트림 함수**
      - **5-1. initHighQualityGate(eType)** - 고품질 CCTV 스트림 시작
        - **역할**: CCTV 관리 페이지용 고품질 스트림 시작
        - **기능**: 저품질과 동일하지만 고품질 스트림 URL 사용
- **서버 파일**
  - `open_close.php`: 차단기 개폐 제어 실행 (DB INSERT 처리)
  - `server/getGateList.php`: 차단기 목록 데이터 조회 API
  - `server/serverGate.php`: 차단기 서버 통신 처리

---

### 6. CCTV
**경로**: `/cctv.html`

#### 주요 기능
- **실시간 영상 스트리밍**: RTSP 프로토콜을 통한 실시간 영상 송출
- **PTZ 카메라 제어**: Pan/Tilt/Zoom 원격 제어 기능
- **녹화 및 재생**: 영상 녹화 저장 및 재생 기능
- **카카오맵 연동 위치 표시**: 카메라 위치를 지도상에 표시
- **다중 화면 모니터링**: 여러 카메라 동시 모니터링

#### 관련 파일
- **메인 CCTV 파일(루트 경로)**
  - `cctv.js`: Node.js 기반 RTSP 스트리밍 서버 (Express, node-rtsp-stream 사용)
    - **WebSocket 스트리밍**: 포트 9001(HIKVISION), 9002(파로스)
    - **REST API 엔드포인트**:
      - `GET /stream-high?conn_ip={IP}&duration={초}`: 고품질 실시간 스트림 (1280x720, 2Mbps)
      - `GET /stream-low?conn_ip={IP}&duration={초}`: 저품질 실시간 스트림 (320x240, 500k)
      - `GET /snap/{camera}`: 즉시 스냅샷 캡처 (hikvision/pharos)
      - `GET /capture/{camera}`: 기본 10초 이미지 캡처 (1fps)
      - `GET /capture/{camera}/{duration}`: 커스텀 시간 이미지 캡처
      - `GET /video/{camera}`: 10초 비디오 캡처 (640x360, MP4)
      - `GET /captures/`: 캡처 파일 목록 조회
      - `GET /captures/{file}`: 캡처 파일 다운로드
- **cctv/ 폴더**
    - `cctvFrame.php`: CCTV 관리 메인 프레임 (카카오맵 연동, 환경변수 설정)
    - `cctv-rtsp-index.js`: RTSP 스트리밍 인덱스 서버        
- **js/ 폴더**
    - `cctv-map.js`: 카카오맵 기반 CCTV 위치 표시 및 툴팁 마커 관리
    - `cctv-modal.js`: CCTV 모달창 제어 (WebSocket 연결, jsmpeg 스트리밍)
- **include/server/ 폴더**
    - `cctvData.php`: DB에서 CCTV 장비 정보 조회 (좌표, IP, 포트)
    - `cctvPopup.php`: CCTV 팝업 UI 생성 (캔버스, 상태표시)
- **include/ 폴더**
    - `popup.php`: 지능형 어시스턴트 팝업 (CCTV 탭 포함)
- **css/ 폴더**
    - `cctv.css`: CCTV 모달, 팝업, 그리드 레이아웃 스타일


---

### 7. SMS (문자 발송)
**경로**: `/sms`

#### 주요 기능
- **개별/단체 문자 발송**
- **발송 내역 관리**
- **주소록 관리**
- **발송 결과 확인**
- **템플릿 메시지 관리**

#### 주요 파일
- `frame/sendMsg.php, sendsms.php`: 문자 발송
- `frame/sendList.php, sendDetail.php`: 발송 내역
- `frame/addrControl.php`: 주소록 관리

---

### 8. Parking (주차 관리)
**경로**: `/parking`

#### 주요 기능
- **차량 입출차 관리**
- **주차 현황 모니터링**
- **LPR(번호판 인식) 연동**
- **주차 통계 및 리포트**
- **PM2 프로세스 관리**

#### 주요 파일
- `frame/InOutCareDay.php, InOutCareMonth.php`: 입출차 관리
- `frame/parkingCar.php, parkingCare.php`: 주차 현황
- `frame/pm2Status.php`: PM2 상태 관리

---

### 9. AdminRegist (사용자 관리)
**경로**: `/adminRegist`

#### 주요 기능
- **사용자 등록/수정/삭제**
- **권한 관리**
- **로그인 이력 관리**
- **사용자 활동 로그**
- **계정 상태 관리**

#### 주요 파일
- `frame/AddUser.php, manageUser.php`: 사용자 관리
- `frame/logList.php, logDetail.php`: 로그 관리
- `server/executeUser.php`: 사용자 실행

---

### 10. Equip (장비 관리) [Root 권한 전용]
**경로**: `/equip`

#### 주요 기능
- **IoT 센서 장비 등록/관리**
- **방송 장비 설정**
- **디스플레이 장비 관리**
- **장비 상태 모니터링**
- **장비 설정 변경**

#### 주요 파일
- `frame/equip.php, equipChange.php`: 장비 등록/수정
- `frame/brdequip.php`: 방송 장비
- `frame/disequip.php, disequipList.php`: 디스플레이 장비

---

### 11. Report (통합 리포트)
**경로**: `/report`

#### 주요 기능
- **종합 통계 리포트**
- **센서 데이터 분석**
- **알림 발생 통계**
- **장비 운영 현황**
- **엑셀 리포트 생성**

#### 주요 파일
- `frame/reportFrame.php`: 리포트 메인
- `server/reportExcel.php`: 엑셀 생성