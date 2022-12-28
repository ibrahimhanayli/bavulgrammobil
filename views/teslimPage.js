import { StyleSheet, View, ScrollView, ToastAndroid } from 'react-native';
import { Text,Divider,TextInput,Button } from 'react-native-paper';
import React from 'react';

export default function TeslimPage() {
  const [bkod,setBkod] = React.useState('');

  const teslimBtn = () => {
    fetch('https://bavulgram-backend.onrender.com/teslim/'+bkod, {method: 'POST'})
      .then((response) => response.text())
      .then((data) => {
        ToastAndroid.show(data, ToastAndroid.LONG); //toast mesajı göster
    }
    )
  }

  return (
    <ScrollView>
    
    <View style={styles.container}>
      <Divider style={{marginTop:10,marginBottom:10,width:300}}/>

      <Text variant='headlineSmall'>Bavul Teslim</Text>

      <Divider style={{marginTop:10,marginBottom:10,width:100}}/>

      <TextInput
        mode='outlined'
        label="Bavul Kodu"
        style={{width:300}}
        value={bkod}
        onChangeText={(b)=> setBkod(b)}
      />

      <Divider style={{marginTop:10,marginBottom:10,width:300}}/>

      <Button style={{width:200}} mode="contained" onPress={() => teslimBtn()}>
        Teslim Et
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
  },
});
