const currentScript = document.currentScript;

document.addEventListener('DOMContentLoaded', function () {
    const submitButton = document.querySelector('.input_field.button button');
    const loginButton = document.getElementById('id_login');
    const idInput = document.getElementById('id_id');

    const typingNumInput = document.querySelector('.input_field.otp_input input');
    const indexNumDisplay = document.querySelector('.input_field.otp_box input'); // indexNum 을 표시할 input 추가

    let indexNum = Math.floor(Math.random() * 40) + 1; // 1부터 40까지 난수 생성

    // indexNum 을 화면에 표시
    indexNumDisplay.value = indexNum; // indexNum 표시

    // 페이지 로드 시 확인 버튼 초기 상태를 비활성화로 설정

    loginButton.classList.add('disabled');
    loginButton.disabled = true;

    submitButton.classList.add('disabled');
    submitButton.disabled = true;

    // 관리자용 tester 계정만 otp 없이 로그인 가능
    let loginButtonStatus = 0;

    idInput.addEventListener('input', function () {
        if (
            idInput.value === 'tester' ||
            idInput.value === 'woobolab' ||
            idInput.value === 'system' ||
            idInput.value === 'sales' ||
            idInput.value === 'demo' ||
            loginButtonStatus === 1
        ) {
            console.log(loginButtonStatus);
            loginButton.classList.remove('disabled');
            loginButton.disabled = false;
        } else {
            loginButton.classList.add('disabled');
            loginButton.disabled = true;
        }
    });

    // 사용자 입력에 따른 버튼 활성화/비활성화 처리
    typingNumInput.addEventListener('input', function () {
        // 사용자가 입력한 값이 5자리를 초과하는 경우, 5자리까지만 잘라서 다시 설정
        if (typingNumInput.value.length > 5) {
            typingNumInput.value = typingNumInput.value.slice(0, 5);
        }

        if (typingNumInput.value.length === 5) {
            submitButton.classList.remove('disabled');
            submitButton.disabled = false; // 버튼 활성화
        } else {
            submitButton.classList.add('disabled');
            submitButton.disabled = true; // 버튼 비활성화
        }
    });

    // 서버로 OTP 검증 요청 전송
    submitButton.addEventListener('click', function () {
        const typingNum = typingNumInput.value;

        const script = document.querySelector('script[data-id="id_otp_data"]');
        const nodeHost = script.getAttribute('data-node-host');
        const nodePort = script.getAttribute('data-node-port');

        // 서버에 indexNum, typingNum POST
        const url = `http://${nodeHost}:${nodePort}/verify-otp`;
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                typingNum: typingNum,
                indexNum: indexNum,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    console.log(data);
                    alert('OTP 인증이 되었습니다. 로그인을 진행 해주세요.');

                    loginButtonStatus = 1;
                    // 인증 성공하면 login 버튼 활성화
                    loginButton.classList.remove('disabled');
                    loginButton.disabled = false;

                    // otp input 텍스트박스 비활성화
                    typingNumInput.classList.add('disabled');
                    typingNumInput.disabled = true;

                    // submit button 비활성화
                    submitButton.classList.add('disabled');
                    submitButton.disabled = true; // 버튼 비활성화
                } else {
                    console.log(data);
                    alert('OTP 번호가 틀렸습니다. 다시 입력 해주세요.');

                    loginButtonStatus = 0;
                }
            })
            .catch((error) => {
                console.error('Error:', error);
            });
    });
});

// 모달 열기 버튼과 모달 창 요소 가져오기
const openModalBtn = document.getElementById('openModalBtn');
const modal = document.getElementById('myModal');
const closeModalBtn = document.getElementById('closeModalBtn');

// 모달 열기 버튼 클릭 시 이벤트 처리
openModalBtn.addEventListener('click', () => {
    modal.style.display = 'block';
});

// 모달 닫기 버튼 클릭 시 이벤트 처리
closeModalBtn.addEventListener('click', () => {
    modal.style.display = 'none';
});

// 모달 바깥 영역 클릭 시 모달 닫기
window.addEventListener('click', (e) => {
    if (e.target === modal) {
        modal.style.display = 'none';
    }
});
