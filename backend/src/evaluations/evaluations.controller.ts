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
import { EvaluationsService } from './evaluations.service';
import { CreateEvaluationDto, UpdateEvaluationDto, GradeEvaluationDto } from './dto';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../auth/guards/roles.guard';
import { Roles } from '../auth/decorators/roles.decorator';
import { UserRole } from '../types/prisma.types';

@Controller('evaluations')
@UseGuards(JwtAuthGuard, RolesGuard)
export class EvaluationsController {
  constructor(private readonly evaluationsService: EvaluationsService) {}

  @Post()
  @HttpCode(HttpStatus.CREATED)
  create(@Body() createEvaluationDto: CreateEvaluationDto, @Request() req) {
    return this.evaluationsService.create(createEvaluationDto, req.user.id, req.user.role);
  }

  @Get()
  findAll(@Request() req, @Query('eventId') eventId?: string) {
    return this.evaluationsService.findAll(req.user.role, req.user.id);
  }

  @Get('report')
  @Roles(UserRole.ADMIN, UserRole.RECURSOS_HUMANOS)
  getReport(@Query('eventId') eventId?: string) {
    return this.evaluationsService.getEvaluationReport(eventId ? parseInt(eventId) : undefined);
  }

  @Get(':id')
  findOne(@Param('id', ParseIntPipe) id: number, @Request() req) {
    return this.evaluationsService.findOne(id, req.user.role, req.user.id);
  }

  @Patch(':id/grade')
  @Roles(UserRole.ADMIN, UserRole.RECURSOS_HUMANOS)
  grade(
    @Param('id', ParseIntPipe) id: number,
    @Body() gradeEvaluationDto: GradeEvaluationDto,
    @Request() req,
  ) {
    return this.evaluationsService.grade(id, gradeEvaluationDto, req.user.id, req.user.role);
  }

  @Patch(':id/answer')
  @Roles(UserRole.EMPLEADO)
  async answerEvaluation(
    @Param('id', ParseIntPipe) id: number,
    @Body() body: { answers: { questionId: number; response: string }[] },
    @Request() req
  ) {
    return this.evaluationsService.answer(id, body.answers, req.user.id);
  }

  @Patch(':id')
  update(
    @Param('id', ParseIntPipe) id: number,
    @Body() updateEvaluationDto: UpdateEvaluationDto,
    @Request() req,
  ) {
    return this.evaluationsService.update(id, updateEvaluationDto, req.user.role, req.user.id);
  }

  @Delete(':id')
  @Roles(UserRole.ADMIN)
  @HttpCode(HttpStatus.NO_CONTENT)
  remove(@Param('id', ParseIntPipe) id: number, @Request() req) {
    return this.evaluationsService.remove(id, req.user.role);
  }
} 