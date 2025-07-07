import {
  Controller,
  Get,
  Post,
  Body,
  Patch,
  Param,
  Delete,
  ParseIntPipe,
  HttpCode,
  HttpStatus,
  UseGuards,
  Request,
  Query,
} from '@nestjs/common';
import { AttendancesService } from './attendances.service';
import { CreateAttendanceDto, UpdateAttendanceDto } from './dto';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../auth/guards/roles.guard';
import { Roles } from '../auth/decorators/roles.decorator';
import { UserRole } from '../types/prisma.types';

@Controller('attendances')
@UseGuards(JwtAuthGuard, RolesGuard)
export class AttendancesController {
  constructor(private readonly attendancesService: AttendancesService) {}

  @Post()
  @HttpCode(HttpStatus.CREATED)
  create(@Body() createAttendanceDto: CreateAttendanceDto, @Request() req) {
    return this.attendancesService.create(createAttendanceDto, req.user.id, req.user.role);
  }

  @Get()
  findAll(@Request() req, @Query('eventId') eventId?: string) {
    return this.attendancesService.findAll(req.user.role, req.user.id, eventId ? parseInt(eventId) : undefined);
  }

  @Get('report')
  @Roles(UserRole.ADMIN, UserRole.RECURSOS_HUMANOS)
  getReport(@Query('eventId') eventId?: string) {
    return this.attendancesService.getAttendanceReport(eventId ? parseInt(eventId) : undefined);
  }

  @Get(':id')
  findOne(@Param('id', ParseIntPipe) id: number, @Request() req) {
    return this.attendancesService.findOne(id, req.user.role, req.user.id);
  }

  @Patch(':id')
  @Roles(UserRole.ADMIN, UserRole.RECURSOS_HUMANOS)
  update(
    @Param('id', ParseIntPipe) id: number,
    @Body() updateAttendanceDto: UpdateAttendanceDto,
    @Request() req,
  ) {
    return this.attendancesService.update(id, updateAttendanceDto, req.user.role);
  }

  @Patch(':id/confirm')
  @Roles(UserRole.EMPLEADO)
  confirmAttendance(@Param('id', ParseIntPipe) id: number, @Request() req) {
    return this.attendancesService.confirm(id, req.user.id);
  }

  @Delete(':id')
  @Roles(UserRole.ADMIN, UserRole.RECURSOS_HUMANOS)
  @HttpCode(HttpStatus.NO_CONTENT)
  remove(@Param('id', ParseIntPipe) id: number, @Request() req) {
    return this.attendancesService.remove(id, req.user.role);
  }
} 