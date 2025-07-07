const axios = require('axios');

const API_BASE_URL = 'http://localhost:3000';

async function testConnection() {
  console.log('🧪 Probando conexión con la API...');
  
  try {
    // Probar endpoint de salud
    console.log('1. Probando endpoint de salud...');
    const healthResponse = await axios.get(`${API_BASE_URL}/`);
    console.log('✅ Endpoint de salud:', healthResponse.data);
    
    // Probar login
    console.log('\n2. Probando login...');
    const loginResponse = await axios.post(`${API_BASE_URL}/auth/login`, {
      email: 'admin@empresa.com',
      password: 'admin123'
    });
    console.log('✅ Login exitoso:', loginResponse.data.user.name);
    
    const token = loginResponse.data.access_token;
    
    // Probar obtener usuarios
    console.log('\n3. Probando obtener usuarios...');
    const usersResponse = await axios.get(`${API_BASE_URL}/users`, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });
    console.log('✅ Usuarios obtenidos:', usersResponse.data.length, 'usuarios');
    
    // Probar obtener eventos
    console.log('\n4. Probando obtener eventos...');
    const eventsResponse = await axios.get(`${API_BASE_URL}/events`, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });
    console.log('✅ Eventos obtenidos:', eventsResponse.data.length, 'eventos');
    
    // Probar obtener asistencias
    console.log('\n5. Probando obtener asistencias...');
    const attendancesResponse = await axios.get(`${API_BASE_URL}/attendances`, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });
    console.log('✅ Asistencias obtenidas:', attendancesResponse.data.length, 'asistencias');
    
    // Probar obtener evaluaciones
    console.log('\n6. Probando obtener evaluaciones...');
    const evaluationsResponse = await axios.get(`${API_BASE_URL}/evaluations`, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });
    console.log('✅ Evaluaciones obtenidas:', evaluationsResponse.data.length, 'evaluaciones');
    
    console.log('\n🎉 ¡Todas las pruebas pasaron! La API está funcionando correctamente.');
    
  } catch (error) {
    console.error('❌ Error en la prueba:', error.response?.data || error.message);
    
    if (error.code === 'ECONNREFUSED') {
      console.log('\n💡 El backend no está ejecutándose. Ejecuta: cd backend && npm run start:dev');
    }
    
    if (error.response?.status === 401) {
      console.log('\n💡 Error de autenticación. Verifica las credenciales.');
    }
    
    if (error.response?.status === 500) {
      console.log('\n💡 Error del servidor. Verifica la configuración de la base de datos.');
    }
  }
}

testConnection(); 