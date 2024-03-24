from django.db import models

# Create your models here.
from django.db import models
from django.contrib.auth.models import User

class MonthlyNumber(models.Model):
    user = models.ForeignKey(User, on_delete=models.CASCADE)
    month = models.DateField()
    number = models.IntegerField()