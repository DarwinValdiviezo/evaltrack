import { Injectable, NotFoundException, ForbiddenException, ConflictException } from '@nestjs/common';
import { PrismaService } from '../prisma.service';
import { CreateAttendanceDto, UpdateAttendanceDto } from './dto';
import { AttendanceStatus, UserRole } from '../types/prisma.types';

@Injectable()
export class AttendancesService {
  constructor(private prisma: PrismaService) {}

  async create(createAttendanceDto: CreateAttendanceDto, userId: number, userRole: UserRole) {
    // Verificar si el evento existe y está activo
    const event = await this.prisma.event.findUnique({
      where: { id: createAttendanceDto.eventId },
    });

    if (!event) {
      throw new NotFoundException('Evento no encontrado');
    }

    if (!event.isActive || event.status !== 'ACTIVE') {
      throw new ForbiddenException('El evento no está disponible para asistencia');
    }

    // Verificar si ya existe una asistencia para este usuario y evento
    const existingAttendance = await this.prisma.attendance.findUnique({
      where: {
        eventId_userId: {
          eventId: createAttendanceDto.eventId,
          userId: userId,
        },
      },
    });

    if (existingAttendance) {
      throw new ConflictException('Ya tienes una asistencia registrada para este evento');
    }

    // Crear la asistencia
    return this.prisma.attendance.create({
      data: {
        eventId: createAttendanceDto.eventId,
        userId: userId,
        status: AttendanceStatus.PENDING,
        notes: createAttendanceDto.notes,
      },
      include: {
        event: {
          select: {
            id: true,
            title: true,
            date: true,
            location: true,
          },
        },
        user: {
          select: {
            id: true,
            name: true,
            email: true,
          },
        },
      },
    });
  }

  async findAll(userRole: UserRole, userId: number, eventId?: number) {
    const where: any = {};

    // Empleados solo ven sus propias asistencias
    if (userRole === UserRole.EMPLEADO) {
      where.userId = userId;
    }
    if (eventId) {
      where.eventId = eventId;
    }
    // RRHH puede filtrar por evento y ver confirmados
    // Si quieres que solo vea confirmados, descomenta la siguiente línea:
    // if (userRole === UserRole.RECURSOS_HUMANOS) where.status = 'CONFIRMED';

    return this.prisma.attendance.findMany({
      where,
      include: {
        event: {
          select: {
            id: true,
            title: true,
            date: true,
            location: true,
            status: true,
          },
        },
        user: {
          select: {
            id: true,
            name: true,
            email: true,
            role: true,
          },
        },
      },
      orderBy: {
        createdAt: 'desc',
      },
    });
  }

  async findOne(id: number, userRole: UserRole, userId: number) {
    const attendance = await this.prisma.attendance.findUnique({
      where: { id },
      include: {
        event: {
          select: {
            id: true,
            title: true,
            date: true,
            location: true,
            status: true,
          },
        },
        user: {
          select: {
            id: true,
            name: true,
            email: true,
            role: true,
          },
        },
      },
    });

    if (!attendance) {
      throw new NotFoundException(`Asistencia con ID ${id} no encontrada`);
    }

    // Empleados solo pueden ver sus propias asistencias
    if (userRole === UserRole.EMPLEADO && attendance.userId !== userId) {
      throw new ForbiddenException('No tienes permisos para ver esta asistencia');
    }

    return attendance;
  }

  async update(id: number, updateAttendanceDto: UpdateAttendanceDto, userRole: UserRole) {
    const attendance = await this.prisma.attendance.findUnique({
      where: { id },
      include: { event: true },
    });

    if (!attendance) {
      throw new NotFoundException(`Asistencia con ID ${id} no encontrada`);
    }

    // Solo RRHH y Admin pueden actualizar asistencias
    if (userRole === UserRole.EMPLEADO) {
      throw new ForbiddenException('No tienes permisos para actualizar asistencias');
    }

    const data: any = {};
    let shouldCreateEvaluation = false;
    if (updateAttendanceDto.status) {
      data.status = updateAttendanceDto.status as AttendanceStatus;
      if (updateAttendanceDto.status === AttendanceStatus.CONFIRMED && attendance.status !== AttendanceStatus.CONFIRMED) {
        data.attendedAt = new Date();
        shouldCreateEvaluation = true;
      }
    }
    if (updateAttendanceDto.notes) {
      data.notes = updateAttendanceDto.notes;
    }

    const updatedAttendance = await this.prisma.attendance.update({
      where: { id },
      data,
      include: {
        event: {
          select: {
            id: true,
            title: true,
            date: true,
            location: true,
          },
        },
        user: {
          select: {
            id: true,
            name: true,
            email: true,
          },
        },
      },
    });

    // Crear evaluación si se confirmó y no existe
    if (shouldCreateEvaluation) {
      const existingEval = await this.prisma.evaluation.findUnique({
        where: {
          eventId_userId: {
            eventId: attendance.eventId,
            userId: attendance.userId,
          },
        },
      });
      if (!existingEval) {
        await this.prisma.evaluation.create({
          data: {
            eventId: attendance.eventId,
            userId: attendance.userId,
            status: 'PENDING', // <-- status permitido por el enum
          },
        });
      }
    }

    return updatedAttendance;
  }

  async remove(id: number, userRole: UserRole) {
    const attendance = await this.prisma.attendance.findUnique({
      where: { id },
    });

    if (!attendance) {
      throw new NotFoundException(`Asistencia con ID ${id} no encontrada`);
    }

    // Solo Admin y RRHH pueden eliminar asistencias
    if (userRole !== UserRole.ADMIN && userRole !== UserRole.RECURSOS_HUMANOS) {
      throw new ForbiddenException('Solo los administradores o RRHH pueden eliminar asistencias');
    }

    return this.prisma.attendance.delete({
      where: { id },
    });
  }

  async getAttendanceReport(eventId?: number) {
    const where: any = {};
    if (eventId) {
      where.eventId = eventId;
    }

    return this.prisma.attendance.groupBy({
      by: ['status'],
      where,
      _count: {
        status: true,
      },
    });
  }

  async confirm(id: number, userId: number) {
    const attendance = await this.prisma.attendance.findUnique({
      where: { id },
      include: { event: true },
    });
    if (!attendance) {
      throw new NotFoundException(`Asistencia con ID ${id} no encontrada`);
    }
    if (attendance.userId !== userId) {
      throw new ForbiddenException('Solo puedes confirmar tu propia asistencia');
    }
    if (attendance.status === AttendanceStatus.CONFIRMED) {
      throw new ConflictException('La asistencia ya fue confirmada');
    }
    // Validar rango horario
    const now = new Date();
    const start = new Date(attendance.event.date);
    const end = new Date(start.getTime() + attendance.event.duration * 60000);
    if (now < start) {
      throw new ForbiddenException('Aún no ha iniciado el evento');
    }
    if (now > end) {
      throw new ForbiddenException('El evento ya finalizó, no puedes confirmar asistencia');
    }
    // Confirmar asistencia
    const updated = await this.prisma.attendance.update({
      where: { id },
      data: {
        status: AttendanceStatus.CONFIRMED,
        attendedAt: now,
      },
    });
    // Crear evaluación si no existe
    const existingEval = await this.prisma.evaluation.findUnique({
      where: {
        eventId_userId: {
          eventId: attendance.eventId,
          userId: attendance.userId,
        },
      },
    });
    if (!existingEval) {
      await this.prisma.evaluation.create({
        data: {
          eventId: attendance.eventId,
          userId: attendance.userId,
          status: 'PENDING',
        },
      });
    }
    return updated;
  }
} 