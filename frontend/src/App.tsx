import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { Toaster } from 'react-hot-toast';
import { AuthProvider, useAuth } from './contexts/AuthContext';
import LoginPage from './pages/LoginPage';
import DashboardPage from './pages/DashboardPage';
import UsersPage from './pages/UsersPage';
import CreateUserPage from './pages/CreateUserPage';
import EditUserPage from './pages/EditUserPage';
import EventsPage from './pages/EventsPage';
import CreateEventPage from './pages/CreateEventPage';
import EditEventPage from './pages/EditEventPage';
import AttendancesPage from './pages/AttendancesPage';
import EvaluationsPage from './pages/EvaluationsPage';
import Layout from './components/Layout';
import LoadingSpinner from './components/LoadingSpinner';
import CreateAttendancePage from './pages/CreateAttendancePage';
import EditAttendancePage from './pages/EditAttendancePage';
import AttendanceDetailPage from './pages/AttendanceDetailPage';
import GradeEvaluationPage from './pages/GradeEvaluationPage';
import AnswerEvaluationPage from './pages/AnswerEvaluationPage';
import CreateEvaluationPage from './pages/CreateEvaluationPage';
import EditEvaluationPage from './pages/EditEvaluationPage';
import EvaluationDetailPage from './pages/EvaluationDetailPage';

// Crear cliente de React Query
const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      retry: 1,
      refetchOnWindowFocus: false,
    },
  },
});

// Componente para rutas protegidas
const ProtectedRoute = ({ children }: { children: React.ReactNode }) => {
  const { isAuthenticated, isLoading } = useAuth();

  if (isLoading) {
    return <LoadingSpinner />;
  }

  if (!isAuthenticated) {
    return <Navigate to="/login" replace />;
  }

  return <>{children}</>;
};

// Componente principal de la aplicación
const AppContent = () => {
  const { isAuthenticated, isLoading } = useAuth();

  if (isLoading) {
    return <LoadingSpinner />;
  }

  return (
    <Router>
      <Routes>
        <Route path="/login" element={!isAuthenticated ? <LoginPage /> : <Navigate to="/" replace />} />
        <Route
          path="/"
          element={
            <ProtectedRoute>
              <Layout />
            </ProtectedRoute>
          }
        >
          <Route index element={<DashboardPage />} />
          <Route path="users" element={<UsersPage />} />
          <Route path="users/create" element={<CreateUserPage />} />
          <Route path="users/edit/:id" element={<EditUserPage />} />
          <Route path="events" element={<EventsPage />} />
          <Route path="events/create" element={<CreateEventPage />} />
          <Route path="events/edit/:id" element={<EditEventPage />} />
          <Route path="attendances" element={<AttendancesPage />} />
          <Route path="attendances/create" element={<CreateAttendancePage />} />
          <Route path="attendances/edit/:id" element={<EditAttendancePage />} />
          <Route path="attendances/:id" element={<AttendanceDetailPage />} />
          <Route path="evaluations" element={<EvaluationsPage />} />
          <Route path="evaluations/create" element={<CreateEvaluationPage />} />
          <Route path="evaluations/edit/:id" element={<EditEvaluationPage />} />
          <Route path="evaluations/:id" element={<EvaluationDetailPage />} />
          <Route path="evaluations/grade/:id" element={<GradeEvaluationPage />} />
          <Route path="evaluations/answer/:id" element={<AnswerEvaluationPage />} />
        </Route>
      </Routes>
    </Router>
  );
};

function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <AuthProvider>
        <AppContent />
        <Toaster
          position="top-right"
          toastOptions={{
            duration: 4000,
            style: {
              background: '#363636',
              color: '#fff',
            },
            success: {
              duration: 3000,
              iconTheme: {
                primary: '#22c55e',
                secondary: '#fff',
              },
            },
            error: {
              duration: 5000,
              iconTheme: {
                primary: '#ef4444',
                secondary: '#fff',
              },
            },
          }}
        />
      </AuthProvider>
    </QueryClientProvider>
  );
}

export default App;
