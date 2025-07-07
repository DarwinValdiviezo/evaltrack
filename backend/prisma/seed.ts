import { PrismaClient } from '@prisma/client';
import { UserRole } from '../src/types/prisma.types';
import * as bcrypt from 'bcrypt';

const prisma = new PrismaClient();

async function main() {
  console.log('🌱 Iniciando seed de la base de datos...');

  // Crear usuario administrador
  const adminPassword = await bcrypt.hash('admin123', 12);
  const admin = await prisma.user.upsert({
    where: { email: 'admin@empresa.com' },
    update: {},
    create: {
      email: 'admin@empresa.com',
      password: adminPassword,
      name: 'Administrador',
      role: UserRole.ADMIN,
    },
  });

  // Crear usuario de Recursos Humanos
  const hrPassword = await bcrypt.hash('hr123', 12);
  const hr = await prisma.user.upsert({
    where: { email: 'hr@empresa.com' },
    update: {},
    create: {
      email: 'hr@empresa.com',
      password: hrPassword,
      name: 'Recursos Humanos',
      role: UserRole.RECURSOS_HUMANOS,
    },
  });

  // Crear empleados de ejemplo
  const employeePassword = await bcrypt.hash('empleado123', 12);
  const employees = await Promise.all([
    prisma.user.upsert({
      where: { email: 'empleado1@empresa.com' },
      update: {},
      create: {
        email: 'empleado1@empresa.com',
        password: employeePassword,
        name: 'Juan Pérez',
        role: UserRole.EMPLEADO,
      },
    }),
    prisma.user.upsert({
      where: { email: 'empleado2@empresa.com' },
      update: {},
      create: {
        email: 'empleado2@empresa.com',
        password: employeePassword,
        name: 'María García',
        role: UserRole.EMPLEADO,
      },
    }),
    prisma.user.upsert({
      where: { email: 'empleado3@empresa.com' },
      update: {},
      create: {
        email: 'empleado3@empresa.com',
        password: employeePassword,
        name: 'Carlos López',
        role: UserRole.EMPLEADO,
      },
    }),
  ]);

  // Crear eventos de ejemplo
  const events = await Promise.all([
    prisma.event.create({
      data: {
        title: 'Capacitación en Liderazgo',
        description: 'Taller para desarrollar habilidades de liderazgo efectivo',
        date: new Date('2024-02-15T09:00:00Z'),
        duration: 120,
        location: 'Sala de Conferencias A',
        maxAttendees: 20,
        creatorId: admin.id,
      },
    }),
    prisma.event.create({
      data: {
        title: 'Seguridad en el Trabajo',
        description: 'Capacitación sobre protocolos de seguridad laboral',
        date: new Date('2024-02-20T14:00:00Z'),
        duration: 90,
        location: 'Auditorio Principal',
        maxAttendees: 50,
        creatorId: admin.id,
      },
    }),
    prisma.event.create({
      data: {
        title: 'Comunicación Efectiva',
        description: 'Mejora tus habilidades de comunicación en el trabajo',
        date: new Date('2024-02-25T10:00:00Z'),
        duration: 180,
        location: 'Sala de Capacitación B',
        maxAttendees: 15,
        creatorId: admin.id,
      },
    }),
  ]);

  // Crear preguntas de ejemplo para cada evento
  for (const event of events) {
    await prisma.question.createMany({
      data: [
        { eventId: event.id, text: '¿Qué aprendiste en este evento?' },
        { eventId: event.id, text: '¿Qué mejorarías para la próxima vez?' },
      ],
    });
  }

  // Crear asistencias confirmadas para empleados en todos los eventos
  const allAttendances: any[] = [];
  for (const event of events) {
    for (const emp of employees) {
      const attendance = await prisma.attendance.create({
        data: {
          eventId: event.id,
          userId: emp.id,
          status: 'CONFIRMED',
          attendedAt: new Date(event.date.getTime() + 60 * 60 * 1000), // 1h después del inicio
        },
      });
      allAttendances.push(attendance);
    }
  }

  // Crear evaluaciones para cada empleado en cada evento
  const allEvaluations: any[] = [];
  for (const attendance of allAttendances) {
    const evaluation = await prisma.evaluation.create({
      data: {
        eventId: attendance.eventId,
        userId: attendance.userId,
        status: 'SUBMITTED',
        submittedAt: new Date(),
      },
    });
    allEvaluations.push(evaluation);
  }

  // Crear respuestas de ejemplo para la primera evaluación de cada empleado
  for (const evaluation of allEvaluations.filter((_, idx) => idx % events.length === 0)) {
    const eventQuestions = await prisma.question.findMany({ where: { eventId: evaluation.eventId } });
    for (const q of eventQuestions) {
      await prisma.answer.create({
        data: {
          evaluationId: evaluation.id,
          questionId: q.id,
          response: `Respuesta de ejemplo a: ${q.text}`,
        },
      });
    }
    // Marcar como calificada una de las evaluaciones
    await prisma.evaluation.update({
      where: { id: evaluation.id },
      data: {
        status: 'GRADED',
        score: 9,
        feedback: '¡Excelente participación!',
        gradedAt: new Date(),
        graderId: hr.id,
      },
    });
  }

  console.log('✅ Seed completado exitosamente!');
  console.log('');
  console.log('👥 Usuarios creados:');
  console.log(`   Admin: admin@empresa.com / admin123`);
  console.log(`   RRHH: hr@empresa.com / hr123`);
  console.log(`   Empleados: empleado1@empresa.com / empleado123`);
  console.log('');
  console.log('📅 Eventos creados:', events.length);
}

main()
  .catch((e) => {
    console.error('❌ Error durante el seed:', e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  }); 