import { StyleSheet, View, ScrollView,ToastAndroid } from 'react-native';
import { Text,Divider,TextInput,Button } from 'react-native-paper';
import React,{ useState } from 'react';

export default function KayitPage() {
  const [veriler, setVeriler] = useState({
    ad: '',
    soyad: '',
    tel: '',
    eposta: '',
    adres: '',
    ucret: '',
  });

  const kaydet = () => {
  //
  fetch('https://bavulgram-backend.onrender.com/kayit', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      ad: veriler.ad,
      soyad: veriler.soyad,
      tel: veriler.tel,
      eposta: veriler.eposta,
      adres: veriler.adres,
      ucret: veriler.ucret,
    }),

  })
    .then((response) => response.text())
    .then((data) => {
      ToastAndroid.show(data, ToastAndroid.LONG); //toast mesajı göster
  })

  };


  return (
    <ScrollView>
    
    <View style={styles.container}>
      <Divider style={{marginTop:10,marginBottom:10,width:300}}/>

      <Text variant='headlineSmall'>Kayıt Oluştur</Text>

      <Divider style={{marginTop:10,marginBottom:10,width:100}}/>

      <TextInput
        mode='outlined'
        label="Ad"
        style={{width:300}}
        value={veriler.ad}
        onChangeText={ad => setVeriler({ ...veriler, ad })}
      />

      <TextInput
        mode='outlined'
        label="Soyad"
        style={{width:300}}
        value={veriler.soyad}
        onChangeText={soyad => setVeriler({ ...veriler, soyad })}
      />

      <TextInput
        mode='outlined'
        label="Telefon"
        style={{width:300}}
        value={veriler.tel}
        onChangeText={tel => setVeriler({ ...veriler, tel })}
      />

      <TextInput
        mode='outlined'
        label="E-Posta"
        style={{width:300}}
        value={veriler.eposta}
        onChangeText={eposta => setVeriler({ ...veriler, eposta })}
      />

      <TextInput
        mode='outlined'
        label="Adres"
        style={{width:300}}
        value={veriler.adres}
        onChangeText={adres => setVeriler({ ...veriler, adres })}
      />

      <TextInput
        mode='outlined'
        label="Ücret"
        
        style={{width:300}}
        value={veriler.ucret}
        onChangeText={ucret => setVeriler({ ...veriler, ucret })}
      />

      <Divider style={{marginTop:10,marginBottom:10,width:300}}/>

      <Button style={{width:200}}  mode="contained" onPress={()=>kaydet()}>
        Kayıt Oluştur
      </Button>
    </View>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    padding:5,
    flex: 1,
    backgroundColor: '#fff',
    alignItems: 'center',
  }
});
