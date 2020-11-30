/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('email_templates', {
    email_template_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    code: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    template_name: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    from: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    replyto: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    subject: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    htmlbody: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    textbody: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    signature: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    version: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'email_templates'
  });
};
