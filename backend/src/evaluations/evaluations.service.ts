import { Injectable, NotFoundException, ForbiddenException, ConflictException } from '@nestjs/common';
import { PrismaService } from '../prisma.service';
import { CreateEvaluationDto, UpdateEvaluationDto, GradeEvaluationDto } from './dto';
import { EvaluationStatus, UserRole } from '../types/prisma.types';

@Injectable()
export class EvaluationsService {
  constructor(private prisma: PrismaService) {}

  async create(createEvaluationDto: CreateEvaluationDto, userId: number, userRole: UserRole) {
    // Verificar si el evento existe
    const event = await this.prisma.event.findUnique({
      where: { id: createEvaluationDto.eventId },
    });

    if (!event) {
      throw new NotFoundException('Evento no encontrado');
    }

    // Verificar si el usuario tiene asistencia confirmada al evento
    const attendance = await this.prisma.attendance.findUnique({
      where: {
        eventId_userId: {
          eventId: createEvaluationDto.eventId,
          userId: userId,
        },
      },
    });

    if (!attendance || attendance.status !== 'CONFIRMED') {
      throw new ForbiddenException('Debes tener asistencia confirmada para crear una evaluación');
    }

    // Verificar si ya existe una evaluación para este usuario y evento
    const existingEvaluation = await this.prisma.evaluation.findUnique({
      where: {
        eventId_userId: {
          eventId: createEvaluationDto.eventId,
          userId: userId,
        },
      },
    });

    if (existingEvaluation) {
      throw new ConflictException('Ya tienes una evaluación para este evento');
    }

    // Crear la evaluación
    return this.prisma.evaluation.create({
      data: {
        eventId: createEvaluationDto.eventId,
        userId: userId,
        status: EvaluationStatus.SUBMITTED,
        feedback: createEvaluationDto.feedback,
        submittedAt: new Date(),
      },
      include: {
        event: {
          select: {
            id: true,
            title: true,
            date: true,
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

  async findAll(userRole: UserRole, userId: number) {
    const where: any = {};

    // Empleados solo ven sus propias evaluaciones
    if (userRole === UserRole.EMPLEADO) {
      where.userId = userId;
    } else {
      // Solo evaluaciones con asistencia confirmada para RRHH/Admin
      where.user = {
        attendances: {
          some: {
            status: 'CONFIRMED',
          },
        },
      };
    }

    return this.prisma.evaluation.findMany({
      where,
      include: {
        event: {
          select: {
            id: true,
            title: true,
            date: true,
            questions: {
              select: {
                id: true,
                text: true,
              },
            },
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
        grader: {
          select: {
            id: true,
            name: true,
            email: true,
          },
        },
        answers: {
          select: {
            id: true,
            questionId: true,
            response: true,
          },
        },
        attendance: true,
      },
      orderBy: {
        createdAt: 'desc',
      },
    });
  }

  async findOne(id: number, userRole: UserRole, userId: number) {
    const evaluation = await this.prisma.evaluation.findUnique({
      where: { id },
      include: {
        event: {
          select: {
            id: true,
            title: true,
            date: true,
            type: true,
            questions: {
              select: {
                id: true,
                text: true,
              },
            },
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
        grader: {
          select: {
            id: true,
            name: true,
            email: true,
          },
        },
        answers: {
          select: {
            id: true,
            questionId: true,
            response: true,
          },
        },
      },
    });

    if (!evaluation) {
      throw new NotFoundException(`Evaluación con ID ${id} no encontrada`);
    }

    // Empleados solo pueden ver sus propias evaluaciones
    if (userRole === UserRole.EMPLEADO && evaluation.userId !== userId) {
      throw new ForbiddenException('No tienes permisos para ver esta evaluación');
    }

    return evaluation;
  }

  async grade(id: number, gradeEvaluationDto: GradeEvaluationDto, graderId: number, userRole: UserRole) {
    const evaluation = await this.prisma.evaluation.findUnique({
      where: { id },
      include: { event: true },
    });

    if (!evaluation) {
      throw new NotFoundException(`Evaluación con ID ${id} no encontrada`);
    }

    // Solo RRHH y Admin pueden calificar evaluaciones
    if (userRole === UserRole.EMPLEADO) {
      throw new ForbiddenException('No tienes permisos para calificar evaluaciones');
    }

    if (evaluation.status === EvaluationStatus.GRADED) {
      throw new ConflictException('Esta evaluación ya ha sido calificada');
    }

    return this.prisma.evaluation.update({
      where: { id },
      data: {
        score: gradeEvaluationDto.score,
        feedback: gradeEvaluationDto.feedback,
        graderId: graderId,
        status: 'GRADED',
        gradedAt: new Date(),
      },
      include: {
        event: {
          select: {
            id: true,
            title: true,
            date: true,
          },
        },
        user: {
          select: {
            id: true,
            name: true,
            email: true,
          },
        },
        grader: {
          select: {
            id: true,
            name: true,
            email: true,
          },
        },
      },
    });
  }

  async update(id: number, updateEvaluationDto: UpdateEvaluationDto, userRole: UserRole, userId: number) {
    const evaluation = await this.prisma.evaluation.findUnique({
      where: { id },
    });

    if (!evaluation) {
      throw new NotFoundException(`Evaluación con ID ${id} no encontrada`);
    }

    // Solo el autor puede actualizar su evaluación antes de ser calificada (excepto RRHH/Admin)
    if (userRole === UserRole.EMPLEADO && evaluation.userId !== userId) {
      throw new ForbiddenException('Solo puedes actualizar tus propias evaluaciones');
    }

    if (evaluation.status === EvaluationStatus.GRADED) {
      throw new ForbiddenException('No puedes actualizar una evaluación ya calificada');
    }

    return this.prisma.evaluation.update({
      where: { id },
      data: {
        feedback: updateEvaluationDto.feedback,
      },
      include: {
        event: {
          select: {
            id: true,
            title: true,
            date: true,
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

  async remove(id: number, userRole: UserRole) {
    const evaluation = await this.prisma.evaluation.findUnique({
      where: { id },
    });

    if (!evaluation) {
      throw new NotFoundException(`Evaluación con ID ${id} no encontrada`);
    }

    // Solo Admin puede eliminar evaluaciones
    if (userRole !== UserRole.ADMIN) {
      throw new ForbiddenException('Solo los administradores pueden eliminar evaluaciones');
    }

    return this.prisma.evaluation.delete({
      where: { id },
    });
  }

  async getEvaluationReport(eventId?: number) {
    const where: any = {};
    if (eventId) {
      where.eventId = eventId;
    }

    return this.prisma.evaluation.groupBy({
      by: ['status'],
      where,
      _count: {
        status: true,
      },
      _avg: {
        score: true,
      },
    });
  }

  async answer(evaluationId: number, answers: { questionId: number; response: string }[], userId: number) {
    // Verificar que la evaluación existe y pertenece al usuario
    const evaluation = await this.prisma.evaluation.findUnique({
      where: { id: evaluationId },
      include: { event: { select: { questions: true } } },
    });
    if (!evaluation) {
      throw new NotFoundException('Evaluación no encontrada');
    }
    if (evaluation.userId !== userId) {
      throw new ForbiddenException('No tienes permisos para responder esta evaluación');
    }
    // Guardar o actualizar respuestas
    for (const ans of answers) {
      await this.prisma.answer.upsert({
        where: {
          evaluationId_questionId: {
            evaluationId,
            questionId: ans.questionId,
          },
        },
        update: { response: ans.response },
        create: {
          evaluationId,
          questionId: ans.questionId,
          response: ans.response,
        },
      });
    }
    // Si respondió todas las preguntas, marcar como SUBMITTED (Completada)
    const totalQuestions = evaluation.event.questions.length;
    const totalAnswered = answers.filter(a => a.response && a.response.trim() !== '').length;
    if (totalAnswered === totalQuestions && totalQuestions > 0) {
      await this.prisma.evaluation.update({
        where: { id: evaluationId },
        data: { status: 'SUBMITTED' },
      });
    }
    return { success: true };
  }
} 