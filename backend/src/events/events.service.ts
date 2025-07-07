import { Injectable, NotFoundException, ForbiddenException } from '@nestjs/common';
import { PrismaService } from '../prisma.service';
import { CreateEventDto, UpdateEventDto } from './dto';
import { UserRole } from '../types/prisma.types';

@Injectable()
export class EventsService {
  constructor(private prisma: PrismaService) {}

  async create(createEventDto: CreateEventDto, creatorId: number) {
    const { preguntas, ...eventData } = createEventDto;
    const event = await this.prisma.event.create({
      data: {
        ...eventData,
        date: new Date(createEventDto.date),
        creatorId,
      },
      include: {
        creator: {
          select: {
            id: true,
            name: true,
            email: true,
          },
        },
      },
    });
    if (preguntas && preguntas.length > 0) {
      await this.prisma.question.createMany({
        data: preguntas.map(text => ({ text, eventId: event.id })),
      });
    }
    return this.findOne(event.id, UserRole.ADMIN);
  }

  async findAll(userRole: UserRole, userId: number) {
    const where: any = {};

    // Solo mostrar eventos activos para empleados y RRHH
    if (userRole === UserRole.EMPLEADO || userRole === UserRole.RECURSOS_HUMANOS) {
      where.isActive = true;
    }
    // Los administradores pueden ver todos los eventos (activos e inactivos)

    return this.prisma.event.findMany({
      where,
      include: {
        creator: {
          select: {
            id: true,
            name: true,
            email: true,
          },
        },
        _count: {
          select: {
            attendances: true,
          },
        },
      },
      orderBy: {
        date: 'asc',
      },
    });
  }

  async findOne(id: number, userRole: UserRole) {
    const event = await this.prisma.event.findUnique({
      where: { id },
      include: {
        creator: {
          select: {
            id: true,
            name: true,
            email: true,
          },
        },
        attendances: {
          include: {
            user: {
              select: {
                id: true,
                name: true,
                email: true,
              },
            },
          },
        },
        evaluations: {
          include: {
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
        },
        questions: true,
      },
    });

    if (!event) {
      throw new NotFoundException(`Evento con ID ${id} no encontrado`);
    }

    // Solo mostrar eventos activos para empleados y RRHH
    if ((userRole === UserRole.EMPLEADO || userRole === UserRole.RECURSOS_HUMANOS) && !event.isActive) {
      throw new ForbiddenException('No tienes permisos para ver este evento');
    }

    return event;
  }

  async update(id: number, updateEventDto: UpdateEventDto, userId: number) {
    const event = await this.prisma.event.findUnique({
      where: { id },
      include: { creator: true },
    });

    if (!event) {
      throw new NotFoundException(`Evento con ID ${id} no encontrado`);
    }

    // Solo el creador puede editar el evento
    if (event.creatorId !== userId) {
      throw new ForbiddenException('Solo el creador puede editar este evento');
    }

    const { preguntas, ...eventData } = updateEventDto;
    if (preguntas) {
      // Eliminar preguntas existentes
      await this.prisma.question.deleteMany({ where: { eventId: id } });
      // Crear nuevas preguntas
      if (preguntas.length > 0) {
        await this.prisma.question.createMany({
          data: preguntas.map(text => ({ text, eventId: id })),
        });
      }
    }
    if (eventData.date) {
      eventData.date = new Date(eventData.date).toISOString();
    }
    await this.prisma.event.update({
      where: { id },
      data: eventData,
    });
    return this.findOne(id, UserRole.ADMIN);
  }

  async toggleStatus(id: number, userId: number) {
    const event = await this.prisma.event.findUnique({
      where: { id },
      include: { creator: true },
    });

    if (!event) {
      throw new NotFoundException(`Evento con ID ${id} no encontrado`);
    }

    // Solo el creador puede cambiar el estado
    if (event.creatorId !== userId) {
      throw new ForbiddenException('Solo el creador puede cambiar el estado de este evento');
    }

    return this.prisma.event.update({
      where: { id },
      data: { isActive: !event.isActive },
      include: {
        creator: {
          select: {
            id: true,
            name: true,
            email: true,
          },
        },
      },
    });
  }

  async remove(id: number, userId: number) {
    const event = await this.prisma.event.findUnique({
      where: { id },
      include: { creator: true },
    });

    if (!event) {
      throw new NotFoundException(`Evento con ID ${id} no encontrado`);
    }

    // Solo el creador puede eliminar el evento
    if (event.creatorId !== userId) {
      throw new ForbiddenException('Solo el creador puede eliminar este evento');
    }

    return this.prisma.event.update({
      where: { id },
      data: { isActive: false },
    });
  }
}
