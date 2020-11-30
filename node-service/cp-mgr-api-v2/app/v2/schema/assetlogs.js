/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('assetlogs', {
    assetid: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    modification_by: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    employee: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    modification_date: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    modification_time: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    statusid: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    invoiceno: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    assetcategoryid: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    agencyid: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    model: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    serialno: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    invoicedate: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    invoiceamount: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    comments: {
      type: DataTypes.STRING(200),
      allowNull: true
    },
    hostname: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    rentalinfo: {
      type: DataTypes.STRING(10),
      allowNull: false,
      defaultValue: 'No'
    },
    rentalreference: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    rentalstartdate: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    rentalenddate: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    rentalvalue: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    assetidentifier: {
      type: DataTypes.STRING(25),
      allowNull: true
    },
    ipaddress: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    auditstatus: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '1'
    },
    assigned_type: {
      type: DataTypes.STRING(50),
      allowNull: true,
      defaultValue: 'employee'
    },
    assigned_agency: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    assigned_bg: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    parent_assetid: {
      type: DataTypes.STRING(50),
      allowNull: true,
      defaultValue: '0'
    },
    paymentref: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'assetlogs'
  });
};
