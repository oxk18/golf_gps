# golf_gps
그누보드용 골프 GOLF GPS &amp; SCORE BOARD &amp; 스윙 분석 with GOOGLE MAP API 

그누보드용 GOLF GPS & SCORE BOARD입니다.

골프칠때 GOLF GPS가 거의 유료라 무료로 쓰고자 함 만들어 봤습니다.

교육용이 아니고 실제 필드에서 테스트해봤습니다.

실전용입니다.

 

지도를 google map api로 쓰기때문에 google api가 필요합니다.

<!-- Google Maps API 로드 -->

<script async defer

src="https://maps.googleapis.com/maps/api/js?key=YOUR-GOOGLE-API&libraries=geometry,places&callback=initMap">

</script>

카카오맵은 여기선 지원이 안돼 만들지못하네요...

 

비밀번호를 설정해서 비공개로 사용하실수도 있지만, 지금은 비밀번호없이 사용할수 있게 해놨습니다.

비공개는 주석처리를 없애시고 비밀번호를 넣으시면 됩니다.

google map api가 free이나 한달에 어느 정도 이상사용하면 요금이 부과되기에 비공개가능하게 해놨습니다.

 

골프장은 현재는 미국 조지아 골프장인데 

    { name: "Sky Valley Country Club", address: "568 Sky Valley Way, Sky Valley, GA 30537" },

    { name: "Fairfield Plantation Golf & Country Club", address: "7500 Monticello Dr, Villa Rica, GA 30180" },

    { name: "Cateechee", address: "140 Cateechee Trl, Hartwell, Georgia 30643" },

이런 스타일로 한국 골프장 주소를 불러오시면 됩니다.

초기 DEFAULT MAP위치가

// 기본 좌표 (예: 미국 골프장 중심부)

    const defaultLocation = { lat: 34.0887, lng: -84.0989 };  

이렇게 설정되있습니다.

이 좌표를 본인이 원하시는 좌표로 수정해주세요.

 

주소입력이나 골프장 선택으로 골프장으로 맵이 바로 변경되며

화살표 찍으면 거리가 나오면서 남,녀,초보,아마추어,프로 5가지 조건에 맞춰서 추천클럽이 뜹니다.

스코어는 18홀까지만 24시간 db없이 기록할수 있게 해놨습니다. 입력할때마다 1,2,3위 금은동 순위 메깁니다.

물론 초기화하면 다시 기록 시작합니다.

 

자기 자신의 스윙동영상을 업로드해서 프로동영상괴 선을 그어가며 자신의 스윙을 비교할수 있게 해좠습니다.

핸폰,데스크탑 겸용입니다.
